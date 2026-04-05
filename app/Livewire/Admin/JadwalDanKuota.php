<?php

namespace App\Livewire\Admin;

use App\Models\OperasionalSetting;
use App\Models\VisitSession;
use Livewire\Component;

class JadwalDanKuota extends Component
{
    // Tab management
    public string $activeTab = 'kelola-sesi';

    // Properties for Visit Sessions CRUD
    public array $sessions = [];

    public bool $showSessionModal = false;

    public bool $editSessionMode = false;

    public ?int $editingSessionId = null;

    // Session form properties
    public string $sesi_nama = '';

    public string $sesi_kode = '';

    public string $sesi_jam_buka = '08:00';

    public string $sesi_jam_tutup = '12:00';

    public int $sesi_kuota = 50;

    public int $sesi_urutan = 1;

    public string $sesi_keterangan = '';

    // Hari Libur properties
    public array $hari_libur_mingguan = [];

    public string $tanggal_libur_baru = '';

    public string $keterangan_libur = '';

    public array $tanggal_libur_khusus = [];

    public string $status_default = 'buka';

    // Show modals
    public bool $showAddHolidayModal = false;

    protected array $sessionRules = [
        'sesi_nama' => 'required|string|max:50',
        'sesi_kode' => 'required|string|max:20|unique:visit_sessions,kode_sesi',
        'sesi_jam_buka' => 'required|date_format:H:i',
        'sesi_jam_tutup' => 'required|date_format:H:i|after:sesi_jam_buka',
        'sesi_kuota' => 'required|integer|min:1|max:500',
        'sesi_urutan' => 'required|integer|min:1',
        'sesi_keterangan' => 'nullable|string|max:255',
    ];

    protected array $holidayRules = [
        'tanggal_libur_baru' => 'required|date',
        'keterangan_libur' => 'nullable|string|max:255',
    ];

    public function mount(): void
    {
        $this->loadSettings();
        $this->loadSessions();
    }

    public function loadSettings(): void
    {
        $settings = OperasionalSetting::first();

        if ($settings) {
            $this->hari_libur_mingguan = $settings->hari_libur_mingguan ?? [];
            $this->tanggal_libur_khusus = $settings->tanggal_libur_khusus ?? [];
            $this->status_default = $settings->status_default ?? 'buka';
        } else {
            $this->hari_libur_mingguan = ['Minggu'];
            $this->tanggal_libur_khusus = [];
        }
    }

    public function loadSessions(): void
    {
        $this->sessions = VisitSession::orderBy('urutan')
            ->get()
            ->map(fn ($session) => [
                'id' => $session->id,
                'nama_sesi' => $session->nama_sesi,
                'kode_sesi' => $session->kode_sesi,
                'jam_buka' => $session->jam_buka?->format('H:i') ?? '',
                'jam_tutup' => $session->jam_tutup?->format('H:i') ?? '',
                'kuota_sesi' => $session->kuota_sesi,
                'is_active' => $session->is_active,
                'urutan' => $session->urutan,
                'keterangan' => $session->keterangan,
            ])
            ->toArray();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetErrorBag();
    }

    // Session CRUD Methods
    public function openCreateSessionModal(): void
    {
        $this->resetSessionForm();
        $this->showSessionModal = true;
        $this->editSessionMode = false;
        $this->editingSessionId = null;
    }

    public function openEditSessionModal(int $sessionId): void
    {
        $session = VisitSession::findOrFail($sessionId);

        $this->sesi_nama = $session->nama_sesi;
        $this->sesi_kode = $session->kode_sesi;
        $this->sesi_jam_buka = $session->jam_buka?->format('H:i') ?? '08:00';
        $this->sesi_jam_tutup = $session->jam_tutup?->format('H:i') ?? '12:00';
        $this->sesi_kuota = $session->kuota_sesi;
        $this->sesi_urutan = $session->urutan;
        $this->sesi_keterangan = $session->keterangan ?? '';

        $this->editingSessionId = $sessionId;
        $this->showSessionModal = true;
        $this->editSessionMode = true;
        $this->resetErrorBag();
    }

    public function closeSessionModal(): void
    {
        $this->showSessionModal = false;
        $this->resetSessionForm();
    }

    public function resetSessionForm(): void
    {
        $this->sesi_nama = '';
        $this->sesi_kode = '';
        $this->sesi_jam_buka = '08:00';
        $this->sesi_jam_tutup = '12:00';
        $this->sesi_kuota = 50;
        $this->sesi_urutan = count($this->sessions) + 1;
        $this->sesi_keterangan = '';
        $this->editingSessionId = null;
        $this->resetErrorBag();
    }

    public function saveSession(): void
    {
        $rules = $this->sessionRules;

        // Remove unique constraint for kode_sesi when editing
        if ($this->editSessionMode) {
            $rules['sesi_kode'] = 'required|string|max:20';
        }

        $this->validate($rules);

        if ($this->editSessionMode && $this->editingSessionId) {
            $session = VisitSession::findOrFail($this->editingSessionId);
            $session->update([
                'nama_sesi' => $this->sesi_nama,
                'kode_sesi' => $this->sesi_kode,
                'jam_buka' => $this->sesi_jam_buka,
                'jam_tutup' => $this->sesi_jam_tutup,
                'kuota_sesi' => $this->sesi_kuota,
                'urutan' => $this->sesi_urutan,
                'keterangan' => $this->sesi_keterangan,
            ]);
            session()->flash('success', 'Sesi berhasil diperbarui');
        } else {
            VisitSession::create([
                'nama_sesi' => $this->sesi_nama,
                'kode_sesi' => strtoupper($this->sesi_kode),
                'jam_buka' => $this->sesi_jam_buka,
                'jam_tutup' => $this->sesi_jam_tutup,
                'kuota_sesi' => $this->sesi_kuota,
                'is_active' => true,
                'urutan' => $this->sesi_urutan,
                'keterangan' => $this->sesi_keterangan,
            ]);
            session()->flash('success', 'Sesi baru berhasil ditambahkan');
        }

        $this->closeSessionModal();
        $this->loadSessions();
    }

    public function toggleSessionStatus(int $sessionId): void
    {
        $session = VisitSession::findOrFail($sessionId);
        $session->update(['is_active' => ! $session->is_active]);
        $this->loadSessions();
        session()->flash('success', 'Status sesi diperbarui');
    }

    public function deleteSession(int $sessionId): void
    {
        $session = VisitSession::findOrFail($sessionId);

        // Check if session has any queues
        if ($session->queues()->count() > 0) {
            session()->flash('error', 'Tidak dapat menghapus sesi yang sudah memiliki antrian');

            return;
        }

        $session->delete();
        $this->loadSessions();
        session()->flash('success', 'Sesi berhasil dihapus');
    }

    // Holiday Methods
    public function saveHariLibur(): void
    {
        $settings = OperasionalSetting::first() ?? new OperasionalSetting;

        $settings->fill([
            'status_default' => $this->status_default,
            'hari_libur_mingguan' => $this->hari_libur_mingguan,
            'tanggal_libur_khusus' => $this->tanggal_libur_khusus,
        ]);

        $settings->save();

        session()->flash('success', 'Pengaturan hari libur berhasil disimpan');
    }

    public function openAddHolidayModal(): void
    {
        $this->showAddHolidayModal = true;
        $this->tanggal_libur_baru = '';
        $this->keterangan_libur = '';
        $this->resetErrorBag();
    }

    public function closeAddHolidayModal(): void
    {
        $this->showAddHolidayModal = false;
        $this->tanggal_libur_baru = '';
        $this->keterangan_libur = '';
        $this->resetErrorBag();
    }

    public function addHoliday(): void
    {
        $this->validate($this->holidayRules);

        // Check if date already exists
        foreach ($this->tanggal_libur_khusus as $holiday) {
            $existingDate = is_array($holiday) ? $holiday['tanggal'] : $holiday;
            if ($existingDate === $this->tanggal_libur_baru) {
                $this->addError('tanggal_libur_baru', 'Tanggal ini sudah ada dalam daftar libur');

                return;
            }
        }

        $this->tanggal_libur_khusus[] = [
            'tanggal' => $this->tanggal_libur_baru,
            'keterangan' => $this->keterangan_libur,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        // Sort by date
        usort($this->tanggal_libur_khusus, function ($a, $b) {
            $dateA = is_array($a) ? $a['tanggal'] : $a;
            $dateB = is_array($b) ? $b['tanggal'] : $b;

            return strcmp($dateA, $dateB);
        });

        $this->closeAddHolidayModal();
        $this->saveHariLibur();
    }

    public function removeHoliday(string $tanggal): void
    {
        $this->tanggal_libur_khusus = array_filter($this->tanggal_libur_khusus, function ($holiday) use ($tanggal) {
            $existingDate = is_array($holiday) ? $holiday['tanggal'] : $holiday;

            return $existingDate !== $tanggal;
        });

        // Re-index array
        $this->tanggal_libur_khusus = array_values($this->tanggal_libur_khusus);
        $this->saveHariLibur();
    }

    public function toggleHariLiburMingguan(string $hari): void
    {
        if (in_array($hari, $this->hari_libur_mingguan)) {
            $this->hari_libur_mingguan = array_diff($this->hari_libur_mingguan, [$hari]);
        } else {
            $this->hari_libur_mingguan[] = $hari;
        }

        // Re-index array
        $this->hari_libur_mingguan = array_values($this->hari_libur_mingguan);
    }

    public function render()
    {
        return view('livewire.admin.jadwal-dan-kuota', [
            'allHari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
        ])->layout('layouts.admin', ['title' => 'Atur Jadwal']);
    }
}
