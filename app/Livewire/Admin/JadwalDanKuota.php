<?php

namespace App\Livewire\Admin;

use App\Models\VisitSchedule;
use Livewire\Component;
use Livewire\WithPagination;

class JadwalDanKuota extends Component
{
    use WithPagination;
    
    public $showModal = false;
    public $showBulkModal = false;
    public $editMode = false;
    public $scheduleId = null;
    
    // View mode: 'calendar' | 'list'
    public $viewMode = 'calendar';
    
    // Calendar properties
    public $currentMonth;
    public $currentYear;
    public $selectedDate = null;
    
    // Single schedule properties
    public $tanggal;
    public $sesi = 'PAGI';
    public $kuota_maksimal = 50;
    public $status_jadwal = 'buka';
    public $jam_mulai;
    public $jam_selesai;
    public $keterangan;
    
    // Bulk schedule properties
    public $bulk_tanggal_mulai;
    public $bulk_tanggal_selesai;
    public $bulk_kuota_maksimal = 50;
    public $bulk_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    public $bulk_skip_holidays = true;
    public $bulk_default_status = 'buka';
    public $bulkPreview = [];
    public $showPreview = false;
    
    protected $rules = [
        'tanggal' => 'required|date|after_or_equal:today',
        'sesi' => 'required|in:PAGI,SIANG',
        'kuota_maksimal' => 'required|integer|min:1|max:500',
        'status_jadwal' => 'required|in:buka,tutup',
        'jam_mulai' => 'nullable|date_format:H:i',
        'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
        'keterangan' => 'nullable|string|max:500',
    ];
    
    protected $bulkRules = [
        'bulk_tanggal_mulai' => 'required|date|after_or_equal:today',
        'bulk_tanggal_selesai' => 'required|date|after_or_equal:bulk_tanggal_mulai',
        'bulk_kuota_maksimal' => 'required|integer|min:1|max:500',
        'bulk_hari' => 'required|array|min:1',
        'bulk_hari.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
        'bulk_skip_holidays' => 'boolean',
        'bulk_default_status' => 'required|in:buka,tutup',
    ];
    
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
    }
    
    public function openBulkModal()
    {
        $this->resetBulkForm();
        $this->showBulkModal = true;
        $this->showPreview = false;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    
    public function closeBulkModal()
    {
        $this->showBulkModal = false;
        $this->resetBulkForm();
    }
    
    public function resetForm()
    {
        $this->scheduleId = null;
        $this->tanggal = '';
        $this->sesi = 'PAGI';
        $this->kuota_maksimal = 50;
        $this->status_jadwal = 'buka';
        $this->jam_mulai = '';
        $this->jam_selesai = '';
        $this->keterangan = '';
        $this->resetErrorBag();
    }
    
    public function resetBulkForm()
    {
        $this->bulk_tanggal_mulai = '';
        $this->bulk_tanggal_selesai = '';
        $this->bulk_kuota_maksimal = 50;
        $this->bulk_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $this->bulk_skip_holidays = true;
        $this->bulk_default_status = 'buka';
        $this->bulkPreview = [];
        $this->showPreview = false;
        $this->resetErrorBag();
    }
    
    public function generatePreview()
    {
        $this->validate($this->bulkRules);
        
        $start = \Carbon\Carbon::parse($this->bulk_tanggal_mulai);
        $end = \Carbon\Carbon::parse($this->bulk_tanggal_selesai);
        $hariMap = [
            'Senin' => 1,
            'Selasa' => 2,
            'Rabu' => 3,
            'Kamis' => 4,
            'Jumat' => 5,
            'Sabtu' => 6,
            'Minggu' => 0,
        ];
        $selectedDays = array_map(fn($h) => $hariMap[$h], $this->bulk_hari);
        
        $this->bulkPreview = [];
        
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!in_array($date->dayOfWeek, $selectedDays)) {
                continue;
            }
            
            // Check if holiday (simplified check)
            if ($this->bulk_skip_holidays && $this->isHoliday($date)) {
                continue;
            }
            
            // Check existing schedules
            $existing = VisitSchedule::where('tanggal', $date->format('Y-m-d'))
                ->pluck('sesi')
                ->toArray();
            
            foreach (['PAGI', 'SIANG'] as $sesi) {
                $this->bulkPreview[] = [
                    'tanggal' => $date->format('Y-m-d'),
                    'tanggal_formatted' => $date->format('d M Y'),
                    'hari' => $date->locale('id')->dayName,
                    'sesi' => $sesi,
                    'exists' => in_array($sesi, $existing),
                    'jam_mulai' => $sesi === 'PAGI' ? '08:00' : '13:00',
                    'jam_selesai' => $sesi === 'PAGI' ? '12:00' : '16:00',
                ];
            }
        }
        
        $this->showPreview = true;
    }
    
    private function isHoliday($date)
    {
        $holidays = [
            '2025-01-01', '2025-01-27', '2025-03-29', '2025-04-18',
            '2025-04-20', '2025-05-01', '2025-05-12', '2025-05-29',
            '2025-06-01', '2025-06-07', '2025-06-08', '2025-08-17',
            '2025-09-05', '2025-12-25',
        ];
        return in_array($date->format('Y-m-d'), $holidays);
    }
    
    public function saveBulk()
    {
        $this->validate($this->bulkRules);
        
        if (!$this->showPreview) {
            $this->generatePreview();
        }
        
        $createdCount = 0;
        $skippedCount = 0;
        
        foreach ($this->bulkPreview as $item) {
            if ($item['exists']) {
                $skippedCount++;
                continue;
            }
            
            VisitSchedule::create([
                'tanggal' => $item['tanggal'],
                'sesi' => $item['sesi'],
                'kuota_maksimal' => $this->bulk_kuota_maksimal,
                'kuota_terpakai' => 0,
                'status_jadwal' => $this->bulk_default_status,
                'jam_mulai' => $item['jam_mulai'],
                'jam_selesai' => $item['jam_selesai'],
                'keterangan' => 'Bulk generated',
            ]);
            
            $createdCount++;
        }
        
        session()->flash('success', "Berhasil membuat {$createdCount} jadwal, {$skippedCount} dilewati (sudah ada).");
        $this->closeBulkModal();
    }
    
    public function openEditModal($id)
    {
        $schedule = VisitSchedule::findOrFail($id);
        $this->scheduleId = $id;
        $this->tanggal = $schedule->tanggal->format('Y-m-d');
        $this->sesi = $schedule->sesi;
        $this->kuota_maksimal = $schedule->kuota_maksimal;
        $this->status_jadwal = $schedule->status_jadwal;
        $this->jam_mulai = $schedule->jam_mulai ? $schedule->jam_mulai->format('H:i') : null;
        $this->jam_selesai = $schedule->jam_selesai ? $schedule->jam_selesai->format('H:i') : null;
        $this->keterangan = $schedule->keterangan;
        $this->showModal = true;
        $this->editMode = true;
    }
    
    public function save()
    {
        $this->validate();
        
        if ($this->editMode) {
            $schedule = VisitSchedule::findOrFail($this->scheduleId);
            
            if ($this->kuota_maksimal < $schedule->kuota_terpakai) {
                $this->addError('kuota_maksimal', 'Kuota maksimal tidak boleh kurang dari kuota terpakai (' . $schedule->kuota_terpakai . ')');
                return;
            }
            
            $schedule->update([
                'tanggal' => $this->tanggal,
                'sesi' => $this->sesi,
                'kuota_maksimal' => $this->kuota_maksimal,
                'status_jadwal' => $this->status_jadwal,
                'jam_mulai' => $this->jam_mulai,
                'jam_selesai' => $this->jam_selesai,
                'keterangan' => $this->keterangan,
            ]);
            
            session()->flash('success', 'Jadwal berhasil diperbarui');
        } else {
            $exists = VisitSchedule::where('tanggal', $this->tanggal)
                ->where('sesi', $this->sesi)
                ->exists();
                
            if ($exists) {
                $this->addError('sesi', 'Jadwal untuk tanggal dan sesi ini sudah ada');
                return;
            }
            
            VisitSchedule::create([
                'tanggal' => $this->tanggal,
                'sesi' => $this->sesi,
                'kuota_maksimal' => $this->kuota_maksimal,
                'status_jadwal' => $this->status_jadwal,
                'jam_mulai' => $this->jam_mulai,
                'jam_selesai' => $this->jam_selesai,
                'keterangan' => $this->keterangan,
            ]);
            
            session()->flash('success', 'Jadwal berhasil dibuat');
        }
        
        $this->closeModal();
    }
    
    public function toggleStatus($id)
    {
        $schedule = VisitSchedule::findOrFail($id);
        $newStatus = $schedule->status_jadwal === 'buka' ? 'tutup' : 'buka';
        $schedule->update(['status_jadwal' => $newStatus]);
        session()->flash('success', 'Status jadwal diubah menjadi ' . $newStatus);
    }
    
    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }
    
    public function previousMonth()
    {
        if ($this->currentMonth === 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
    }
    
    public function nextMonth()
    {
        if ($this->currentMonth === 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
    }
    
    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->tanggal = $date;
        $this->openCreateModal();
    }
    
    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'calendar' ? 'list' : 'calendar';
    }
    
    public function render()
    {
        if ($this->viewMode === 'list') {
            $schedules = VisitSchedule::orderBy('tanggal', 'desc')
                ->orderBy('sesi')
                ->paginate(20);
                
            return view('livewire.admin.jadwal-dan-kuota', [
                'schedules' => $schedules,
                'calendarData' => null,
            ])->layout('layouts.admin', ['title' => 'Jadwal & Kuota']);
        }
        
        // Calendar view
        $startOfMonth = \Carbon\Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Get all schedules for current month view
        $schedules = VisitSchedule::whereBetween('tanggal', [
            $startOfMonth->copy()->startOfWeek(),
            $endOfMonth->copy()->endOfWeek()
        ])->get();
        
        // Group by date
        $calendarData = [];
        foreach ($schedules as $schedule) {
            $date = $schedule->tanggal->format('Y-m-d');
            if (!isset($calendarData[$date])) {
                $calendarData[$date] = [];
            }
            $calendarData[$date][] = $schedule;
        }
        
        return view('livewire.admin.jadwal-dan-kuota', [
            'schedules' => $schedules,
            'calendarData' => $calendarData,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
        ])->layout('layouts.admin', ['title' => 'Jadwal & Kuota']);
    }
}
