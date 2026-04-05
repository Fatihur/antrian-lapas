<?php

namespace App\Livewire\Public;

use App\Models\VisitSchedule;
use App\Models\VisitQueue;
use App\Models\VisitFollower;
use App\Services\QueueNumberGenerator;
use App\Services\PdfTicketService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class AmbilAntrian extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $totalSteps = 4;
    public $nik_pendaftar = '';
    public $jenis_identitas = 'KTP';
    public $nama_pengunjung = '';
    public $no_hp = '';
    public $hubungan_wbp = '';
    public $nama_wbp = '';
    public $foto_identitas = null;
    public $catatan = '';
    public $followers = [];
    public $tanggal_kunjungan = '';
    public $sesi_kunjungan = '';
    public $availableSchedules = [];
    public $selectedSchedule = null;
    public $submitted = false;
    public $queueData = null;

    public function mount()
    {
        $this->loadAvailableSchedules();
        if (empty($this->followers)) {
            $this->followers = [];
        }
    }

    protected function getRulesForStep($step)
    {
        return match($step) {
            1 => [
                'nik_pendaftar' => 'required|string|min:10|max:25',
                'jenis_identitas' => 'required|in:KTP,SIM,Paspor,KK,Lainnya',
                'nama_pengunjung' => 'required|string|min:3|max:150',
                'no_hp' => 'required|string|regex:/^[0-9]{10,15}$/',
                'hubungan_wbp' => 'required|string|min:2|max:100',
                'nama_wbp' => 'required|string|min:3|max:150',
                'foto_identitas' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'catatan' => 'nullable|string|max:500',
            ],
            2 => [
                'followers' => 'array|max:10',
                'followers.*.nama_pengikut' => 'required|string|min:3|max:150',
                'followers.*.nomor_identitas_pengikut' => 'required|string|min:5|max:25',
                'followers.*.jenis_kelamin_pengikut' => 'required|in:Laki-laki,Perempuan',
            ],
            3 => [
                'tanggal_kunjungan' => 'required|date|after_or_equal:today',
                'sesi_kunjungan' => 'required|in:PAGI,SIANG',
            ],
            default => [],
        };
    }

    protected function getMessages()
    {
        return [
            'nik_pendaftar.required' => 'NIK wajib diisi',
            'nik_pendaftar.min' => 'NIK minimal 10 karakter',
            'jenis_identitas.required' => 'Jenis identitas wajib dipilih',
            'nama_pengunjung.required' => 'Nama lengkap wajib diisi',
            'no_hp.regex' => 'Nomor HP tidak valid',
            'hubungan_wbp.required' => 'Hubungan dengan WBP wajib diisi',
            'nama_wbp.required' => 'Nama WBP wajib diisi',
            'foto_identitas.required' => 'Foto identitas wajib diunggah',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib dipilih',
            'sesi_kunjungan.required' => 'Sesi kunjungan wajib dipilih',
        ];
    }

    public function loadAvailableSchedules()
    {
        // Load schedules and convert to array to avoid Livewire serialization issues
        $schedules = VisitSchedule::open()
            ->whereDate('tanggal', '>=', now())
            ->whereDate('tanggal', '<=', now()->addDays(30))
            ->orderBy('tanggal')
            ->orderBy('sesi')
            ->get();
        
        // Convert to array for Livewire compatibility
        $this->availableSchedules = $schedules->map(function($schedule) {
            return [
                'id' => $schedule->id,
                'tanggal' => $schedule->tanggal->format('Y-m-d'),
                'tanggal_formatted' => $schedule->tanggal->format('d M Y'),
                'sesi' => $schedule->sesi,
                'kuota_maksimal' => $schedule->kuota_maksimal,
                'kuota_terpakai' => $schedule->kuota_terpakai,
                'sisa_kuota' => $schedule->sisa_kuota,
                'status_jadwal' => $schedule->status_jadwal,
                'jam_mulai' => $schedule->jam_mulai,
                'jam_selesai' => $schedule->jam_selesai,
            ];
        })->groupBy('tanggal')->toArray();
    }

    public function addFollower()
    {
        if (count($this->followers) < 10) {
            $this->followers[] = [
                'nama_pengikut' => '',
                'nomor_identitas_pengikut' => '',
                'jenis_kelamin_pengikut' => '',
            ];
        }
    }

    public function removeFollower($index)
    {
        if (isset($this->followers[$index])) {
            unset($this->followers[$index]);
            $this->followers = array_values($this->followers);
        }
    }

    public function nextStep()
    {
        $this->validate($this->getRulesForStep($this->currentStep), $this->getMessages());
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function submit()
    {
        $this->validate($this->getRulesForStep($this->currentStep), $this->getMessages());

        try {
            $queueData = DB::transaction(function () {
                $schedule = VisitSchedule::open()
                    ->where('tanggal', $this->tanggal_kunjungan)
                    ->where('sesi', $this->sesi_kunjungan)
                    ->first();
                
                if (!$schedule || !$schedule->isKuotaAvailable()) {
                    throw new \Exception('Kuota untuk jadwal ini sudah penuh atau jadwal tutup');
                }

                $generator = app(QueueNumberGenerator::class);
                $pdfService = app(PdfTicketService::class);

                $photoPath = $this->foto_identitas->store('identitas', 'public');

                $nomorAntrian = $generator->generate($this->tanggal_kunjungan, $this->sesi_kunjungan);
                $kodeBooking = $generator->generateBookingCode();

                $queue = VisitQueue::create([
                    'visit_schedule_id' => $schedule->id,
                    'kode_booking' => $kodeBooking,
                    'nomor_antrian' => $nomorAntrian,
                    'nik_pendaftar' => $this->nik_pendaftar,
                    'jenis_identitas' => $this->jenis_identitas,
                    'nama_pengunjung' => $this->nama_pengunjung,
                    'no_hp' => $this->no_hp,
                    'hubungan_wbp' => $this->hubungan_wbp,
                    'nama_wbp' => $this->nama_wbp,
                    'foto_identitas' => $photoPath,
                    'catatan' => $this->catatan,
                    'status_antrian' => 'Disetujui',
                    'waktu_daftar' => now(),
                ]);

                foreach ($this->followers as $followerData) {
                    if (!empty($followerData['nama_pengikut'])) {
                        VisitFollower::create([
                            'visit_queue_id' => $queue->id,
                            'nama_pengikut' => $followerData['nama_pengikut'],
                            'nomor_identitas_pengikut' => $followerData['nomor_identitas_pengikut'],
                            'jenis_kelamin_pengikut' => $followerData['jenis_kelamin_pengikut'],
                        ]);
                    }
                }

                $schedule->incrementKuotaTerpakai();

                $pdfPath = $pdfService->generate($queue);
                $queue->update(['pdf_path' => $pdfPath]);

                return $queue->load(['schedule', 'followers']);
            });

            $this->queueData = $queueData;
            $this->submitted = true;
            $this->dispatch('queue-created', ['queueId' => $queueData->id]);

        } catch (\Exception $e) {
            $this->addError('submit', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.public.ambil-antrian')
            ->layout('layouts.app', ['title' => 'Ambil Antrian']);
    }
}
