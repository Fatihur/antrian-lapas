<?php

namespace App\Livewire\Public;

use App\Models\OperasionalSetting;
use App\Models\VisitFollower;
use App\Models\VisitQueue;
use App\Models\VisitSession;
use App\Services\PdfTicketService;
use App\Services\QueueNumberGenerator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

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

    public $availableSessions = [];

    public $selectedSchedule = null;

    public $submitted = false;

    public $queueData = null;

    public function mount()
    {
        $this->loadAvailableSchedules();
        $this->loadAvailableSessions();
        if (empty($this->followers)) {
            $this->followers = [];
        }
    }

    protected function getRulesForStep($step)
    {
        return match ($step) {
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
                'sesi_kunjungan' => 'required|exists:visit_sessions,kode_sesi',
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
            'sesi_kunjungan.exists' => 'Sesi yang dipilih tidak valid',
        ];
    }

    public function loadAvailableSchedules()
    {
        $settings = OperasionalSetting::getSettings();

        if (! $settings || $settings->status_default === 'tutup') {
            $this->availableSchedules = [];

            return;
        }

        // Generate available dates for the next 30 days
        $availableDates = [];
        $startDate = now();
        $endDate = now()->addDays(30);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Check if date is a holiday
            if ($settings->isHariLibur($date)) {
                continue;
            }

            // Get available sessions for this date
            $activeSessions = VisitSession::getActiveSessions();
            $availableSesi = [];
            $totalSisaKuota = 0;

            foreach ($activeSessions as $session) {
                // Check quota for this session on this date
                $usedQuota = VisitQueue::whereDate('tanggal_kunjungan', $date->format('Y-m-d'))
                    ->where('visit_session_id', $session->id)
                    ->whereIn('status_antrian', ['Disetujui', 'Menunggu'])
                    ->count();

                $sisaKuota = max(0, $session->kuota_sesi - $usedQuota);

                if ($sisaKuota > 0) {
                    $availableSesi[] = [
                        'id' => $session->id,
                        'kode' => $session->kode_sesi,
                        'nama' => $session->nama_sesi,
                        'jam' => $session->getInfoOperasional(),
                        'sisa_kuota' => $sisaKuota,
                        'kuota_maksimal' => $session->kuota_sesi,
                    ];
                    $totalSisaKuota += $sisaKuota;
                }
            }

            if (count($availableSesi) > 0) {
                $availableDates[$date->format('Y-m-d')] = [
                    'tanggal' => $date->format('Y-m-d'),
                    'tanggal_formatted' => $date->format('d M Y'),
                    'hari' => $date->locale('id')->dayName,
                    'sesi' => $availableSesi,
                    'total_sisa_kuota' => $totalSisaKuota,
                ];
            }
        }

        $this->availableSchedules = $availableDates;
    }

    public function loadAvailableSessions()
    {
        $this->availableSessions = VisitSession::getActiveSessions()
            ->map(fn ($session) => [
                'id' => $session->id,
                'kode_sesi' => $session->kode_sesi,
                'nama_sesi' => $session->nama_sesi,
                'jam_operasional' => $session->getInfoOperasional(),
                'kuota_sesi' => $session->kuota_sesi,
            ])
            ->toArray();
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
                $settings = OperasionalSetting::getSettings();

                if (! $settings) {
                    throw new \Exception('Pengaturan operasional tidak ditemukan');
                }

                // Check if date is a holiday
                $selectedDate = Carbon::parse($this->tanggal_kunjungan);
                if ($settings->isHariLibur($selectedDate)) {
                    throw new \Exception('Tanggal yang dipilih adalah hari libur');
                }

                // Get session
                $session = VisitSession::where('kode_sesi', $this->sesi_kunjungan)
                    ->where('is_active', true)
                    ->first();

                if (! $session) {
                    throw new \Exception('Sesi yang dipilih tidak tersedia');
                }

                // Check quota availability for this session on this date
                $usedQuota = VisitQueue::whereDate('tanggal_kunjungan', $this->tanggal_kunjungan)
                    ->where('visit_session_id', $session->id)
                    ->whereIn('status_antrian', ['Disetujui', 'Menunggu'])
                    ->count();

                if ($usedQuota >= $session->kuota_sesi) {
                    throw new \Exception('Kuota untuk sesi ini sudah penuh');
                }

                $generator = app(QueueNumberGenerator::class);
                $pdfService = app(PdfTicketService::class);

                $photoPath = $this->foto_identitas->store('identitas', 'public');

                $nomorAntrian = $generator->generate($this->tanggal_kunjungan, $this->sesi_kunjungan);
                $kodeBooking = $generator->generateBookingCode();

                $queue = VisitQueue::create([
                    'visit_session_id' => $session->id,
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
                    'tanggal_kunjungan' => $this->tanggal_kunjungan,
                ]);

                foreach ($this->followers as $followerData) {
                    if (! empty($followerData['nama_pengikut'])) {
                        VisitFollower::create([
                            'visit_queue_id' => $queue->id,
                            'nama_pengikut' => $followerData['nama_pengikut'],
                            'nomor_identitas_pengikut' => $followerData['nomor_identitas_pengikut'],
                            'jenis_kelamin_pengikut' => $followerData['jenis_kelamin_pengikut'],
                        ]);
                    }
                }

                $pdfPath = $pdfService->generate($queue);
                $queue->update(['pdf_path' => $pdfPath]);

                return $queue->load(['session', 'followers']);
            });

            $this->queueData = $queueData;
            $this->submitted = true;
            $this->dispatch('queue-created', ['queueId' => $queueData->id]);

        } catch (\Exception $e) {
            $this->addError('submit', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.public.ambil-antrian')
            ->layout('layouts.app', ['title' => 'Ambil Antrian']);
    }
}
