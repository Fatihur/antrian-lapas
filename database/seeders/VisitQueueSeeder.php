<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\VisitSchedule;
use App\Models\VisitQueue;
use App\Models\VisitFollower;
use App\Models\QueueCall;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VisitQueueSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membuat data antrian kunjungan...');

        // Get admin for verification
        $admin = Admin::where('role', 'super_admin')->first();

        // Create schedules for today and next 7 days
        $this->createSchedules();

        // Create queues with various statuses
        $this->createQueuesForToday($admin);
        $this->createQueuesForTomorrow($admin);
        $this->createQueuesWithDifferentStatuses($admin);

        $this->command->info('');
        $this->command->info('✓ Data antrian berhasil dibuat!');
        $this->command->info('');
        $this->command->info('Ringkasan Data:');
        $this->command->info('- Jadwal: ' . VisitSchedule::count() . ' jadwal');
        $this->command->info('- Antrian: ' . VisitQueue::count() . ' antrian');
        $this->command->info('- Pengikut: ' . VisitFollower::count() . ' pengikut');
        $this->command->info('- Panggilan: ' . QueueCall::count() . ' panggilan');
        $this->command->info('');
        $this->command->info('Status antrian:');
        $this->printStatusSummary();
    }

    private function createSchedules(): void
    {
        $today = Carbon::today();
        
        // Create schedules for 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            // Morning session (PAGI)
            VisitSchedule::firstOrCreate(
                [
                    'tanggal' => $date,
                    'sesi' => 'PAGI',
                ],
                [
                    'kuota_maksimal' => 30,
                    'kuota_terpakai' => 0,
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '12:00:00',
                    'status_jadwal' => 'buka',
                ]
            );

            // Afternoon session (SIANG)
            VisitSchedule::firstOrCreate(
                [
                    'tanggal' => $date,
                    'sesi' => 'SIANG',
                ],
                [
                    'kuota_maksimal' => 25,
                    'kuota_terpakai' => 0,
                    'jam_mulai' => '13:00:00',
                    'jam_selesai' => '16:00:00',
                    'status_jadwal' => 'buka',
                ]
            );
        }

        // Create one closed schedule if doesn't exist
        $closedDate = $today->copy()->addDays(3);
        if (!$closedDate->isWeekend()) {
            VisitSchedule::firstOrCreate(
                [
                    'tanggal' => $closedDate,
                    'sesi' => 'PAGI',
                ],
                [
                    'kuota_maksimal' => 30,
                    'kuota_terpakai' => 0,
                    'status_jadwal' => 'tutup',
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '12:00:00',
                ]
            );
        }
    }

    private function createQueuesForToday(?Admin $admin): void
    {
        $today = Carbon::today();
        
        $schedules = VisitSchedule::whereDate('tanggal', $today)
            ->where('status_jadwal', 'buka')
            ->get();

        foreach ($schedules as $schedule) {
            $existingCount = VisitQueue::where('visit_schedule_id', $schedule->id)->count();
            if ($existingCount > 0) {
                $this->command->info("  - Jadwal {$schedule->tanggal->format('d/m/Y')} {$schedule->sesi} sudah punya {$existingCount} antrian, skip...");
                continue;
            }
            $this->createQueuesForSchedule($schedule, $admin, 15);
        }
    }

    private function createQueuesForTomorrow(?Admin $admin): void
    {
        $tomorrow = Carbon::tomorrow();
        
        $schedules = VisitSchedule::whereDate('tanggal', $tomorrow)
            ->where('status_jadwal', 'buka')
            ->get();

        foreach ($schedules as $schedule) {
            $existingCount = VisitQueue::where('visit_schedule_id', $schedule->id)->count();
            if ($existingCount > 0) {
                $this->command->info("  - Jadwal {$schedule->tanggal->format('d/m/Y')} {$schedule->sesi} sudah punya {$existingCount} antrian, skip...");
                continue;
            }
            $this->createQueuesForSchedule($schedule, $admin, 10);
        }
    }

    private function createQueuesForSchedule($schedule, ?Admin $admin, int $count): void
    {
        $this->command->info("  - Membuat {$count} antrian untuk {$schedule->tanggal->format('d/m/Y')} {$schedule->sesi}...");
        
        $usedNumbers = [];
        $prefixes = ['A', 'B', 'C', 'D', 'E'];
        
        for ($i = 0; $i < $count; $i++) {
            // Generate unique queue number
            do {
                $prefix = $prefixes[array_rand($prefixes)];
                $number = str_pad($i + 1, 3, '0', STR_PAD_LEFT);
                $nomorAntrian = "{$prefix}{$number}";
            } while (in_array($nomorAntrian, $usedNumbers));
            
            $usedNumbers[] = $nomorAntrian;
            $tanggalFormatted = $schedule->tanggal->format('dmY');
            $fullQueueNumber = "{$nomorAntrian}-{$schedule->sesi}-{$tanggalFormatted}";

            // Check if queue already exists
            if (VisitQueue::where('nomor_antrian', $fullQueueNumber)->exists()) {
                continue;
            }

            // Create queue with varying status
            $status = $this->getRandomStatus($i, $count);
            
            $queueData = [
                'visit_schedule_id' => $schedule->id,
                'kode_booking' => strtoupper(uniqid()),
                'nomor_antrian' => $fullQueueNumber,
                'nik_pendaftar' => $this->generateNIK(),
                'jenis_identitas' => 'KTP',
                'nama_pengunjung' => $this->getRandomName(),
                'no_hp' => $this->generatePhone(),
                'hubungan_wbp' => $this->getRandomRelationship(),
                'nama_wbp' => $this->getRandomWBPName(),
                'foto_identitas' => 'identitas/sample.jpg',
                'catatan' => $i % 5 === 0 ? 'Pengunjung pertama kali' : null,
                'status_antrian' => $status,
                'waktu_daftar' => $schedule->tanggal->copy()->subDays(rand(1, 5))->setTime(rand(8, 20), rand(0, 59)),
                'waktu_verifikasi' => in_array($status, ['Disetujui', 'Menunggu Dipanggil', 'Dipanggil', 'Selesai']) ? now() : null,
                'verified_by' => in_array($status, ['Disetujui', 'Menunggu Dipanggil', 'Dipanggil', 'Selesai']) ? ($admin?->id ?? 1) : null,
                'waktu_selesai' => $status === 'Selesai' ? now() : null,
            ];

            $queue = VisitQueue::create($queueData);

            // Create followers (1-4 people)
            $followerCount = rand(1, 4);
            for ($j = 0; $j < $followerCount; $j++) {
                VisitFollower::create([
                    'visit_queue_id' => $queue->id,
                    'nama_pengikut' => $this->getRandomName(),
                    'nomor_identitas_pengikut' => $this->generateNIK(),
                    'jenis_kelamin_pengikut' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                ]);
            }

            // Update quota usage
            $schedule->increment('kuota_terpakai');

            // Create queue call for called/completed queues
            if (in_array($status, ['Dipanggil', 'Selesai'])) {
                $this->createQueueCall($queue, $status);
            }
        }
    }

    private function createQueuesWithDifferentStatuses(?Admin $admin): void
    {
        $schedule = VisitSchedule::whereDate('tanggal', '>=', Carbon::today())
            ->where('status_jadwal', 'buka')
            ->first();

        if (!$schedule) {
            return;
        }

        $this->command->info("  - Membuat contoh antrian dengan berbagai status...");

        // Create specific status examples
        $statuses = [
            'Menunggu Verifikasi' => 2,
            'Disetujui' => 3,
            'Ditolak' => 1,
            'Menunggu Dipanggil' => 2,
            'Dipanggil' => 1,
            'Selesai' => 3,
            'Kedaluwarsa' => 1,
        ];

        $counter = 100; // Start from higher number

        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $tanggalFormatted = $schedule->tanggal->format('dmY');
                $nomorAntrian = "Z" . str_pad($counter++, 3, '0', STR_PAD_LEFT);
                $fullQueueNumber = "{$nomorAntrian}-{$schedule->sesi}-{$tanggalFormatted}";

                // Use firstOrCreate to avoid duplicates
                $queue = VisitQueue::firstOrCreate(
                    ['nomor_antrian' => $fullQueueNumber],
                    [
                        'visit_schedule_id' => $schedule->id,
                        'kode_booking' => strtoupper(uniqid()),
                        'nik_pendaftar' => $this->generateNIK(),
                        'jenis_identitas' => 'KTP',
                        'nama_pengunjung' => $this->getRandomName(),
                        'no_hp' => $this->generatePhone(),
                        'hubungan_wbp' => $this->getRandomRelationship(),
                        'nama_wbp' => $this->getRandomWBPName(),
                        'foto_identitas' => 'identitas/sample.jpg',
                        'status_antrian' => $status,
                        'waktu_daftar' => now()->subDays(rand(1, 3)),
                        'waktu_verifikasi' => in_array($status, ['Disetujui', 'Menunggu Dipanggil', 'Dipanggil', 'Selesai', 'Ditolak']) ? now() : null,
                        'verified_by' => in_array($status, ['Disetujui', 'Menunggu Dipanggil', 'Dipanggil', 'Selesai', 'Ditolak']) ? ($admin?->id ?? 1) : null,
                        'waktu_selesai' => in_array($status, ['Selesai', 'Kedaluwarsa']) ? now() : null,
                        'alasan_penolakan' => $status === 'Ditolak' ? 'Dokumen tidak lengkap' : null,
                    ]
                );

                // Only add followers and calls if queue was just created
                if ($queue->wasRecentlyCreated) {
                    // Add 1-2 followers
                    $followerCount = rand(1, 2);
                    for ($j = 0; $j < $followerCount; $j++) {
                        VisitFollower::create([
                            'visit_queue_id' => $queue->id,
                            'nama_pengikut' => $this->getRandomName(),
                            'nomor_identitas_pengikut' => $this->generateNIK(),
                            'jenis_kelamin_pengikut' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                        ]);
                    }

                    // Create call record for called/completed
                    if (in_array($status, ['Dipanggil', 'Selesai'])) {
                        $this->createQueueCall($queue, $status);
                    }
                }
            }
        }
    }

    private function createQueueCall($queue, string $status): void
    {
        $counters = ['LOKET 1', 'LOKET 2', 'LOKET 3', 'LOKET 4'];
        $counter = $counters[array_rand($counters)];
        
        QueueCall::firstOrCreate(
            [
                'visit_queue_id' => $queue->id,
                'waktu_panggilan' => now()->subMinutes(rand(5, 30)),
            ],
            [
                'called_by' => 1, // admin
                'loket' => $counter,
                'waktu_selesai' => $status === 'Selesai' ? now() : null,
                'status_panggilan' => $status === 'Selesai' ? 'Selesai' : 'Dipanggil',
                'recall_count' => rand(0, 2),
            ]
        );
    }

    private function getRandomStatus(int $index, int $total): string
    {
        $distribution = [
            'Disetujui' => 30,
            'Menunggu Dipanggil' => 20,
            'Dipanggil' => 10,
            'Selesai' => 25,
            'Kedaluwarsa' => 5,
            'Menunggu Verifikasi' => 10,
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($distribution as $status => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'Disetujui';
    }

    private function printStatusSummary(): void
    {
        $statuses = VisitQueue::selectRaw('status_antrian, count(*) as count')
            ->groupBy('status_antrian')
            ->pluck('count', 'status_antrian')
            ->toArray();

        foreach ($statuses as $status => $count) {
            $this->command->info("  - {$status}: {$count}");
        }
    }

    private function generateNIK(): string
    {
        $provinsi = str_pad(rand(11, 91), 2, '0', STR_PAD_LEFT);
        $kota = str_pad(rand(1, 20), 2, '0', STR_PAD_LEFT);
        $kecamatan = str_pad(rand(1, 50), 2, '0', STR_PAD_LEFT);
        $tgl = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
        $bln = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $thn = str_pad(rand(50, 99), 2, '0', STR_PAD_LEFT);
        $uniq = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$provinsi}{$kota}{$kecamatan}{$tgl}{$bln}{$thn}{$uniq}";
    }

    private function generatePhone(): string
    {
        $prefixes = ['0812', '0813', '0856', '0857', '0896', '0895', '0878', '0838'];
        $prefix = $prefixes[array_rand($prefixes)];
        $number = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$number}";
    }

    private function getRandomName(): string
    {
        $names = [
            'Ahmad Fauzi', 'Budi Santoso', 'Citra Dewi', 'Dedi Kurniawan', 'Eka Putri',
            'Fajar Nugroho', 'Gita Maharani', 'Hadi Wijaya', 'Indah Sari', 'Joko Susanto',
            'Kartini Sari', 'Lukman Hakim', 'Maya Anggraini', 'Nur Hidayat', 'Putri Lestari',
            'Rudi Hartono', 'Siti Aminah', 'Teguh Santoso', 'Umi Kalsum', 'Vina Panduwinata',
            'Wawan Setiawan', 'Yanti Suryani', 'Zainal Abidin', 'Adi Nugroho', 'Beni Pratama',
            'Caca Handika', 'Dodi Sudrajat', 'Euis Sukaesih', 'Ferry Irawan', 'Gugun Gondrong',
            'Hani Farida', 'Iwan Fals', 'Jajang Cijantung', 'Kiki Fatmala', 'Lilis Sugiarti',
            'Maman Suryaman', 'Neni Marlena', 'Opan Sujiwo', 'Popon Siti', 'Qomar Sucipto',
        ];

        return $names[array_rand($names)];
    }

    private function getRandomWBPName(): string
    {
        $wbpNames = [
            'Andi Wijaya', 'Bambang Sutrisno', 'Candra Wijaksana', 'Dadang Kusuma', 'Eddy Supriyadi',
            'Fadli Arif', 'Gunawan Setyo', 'Hendra Kurniawan', 'Irfan Hakim', 'Jajang Sukma',
            'Kuswanto', 'Lukman Sardi', 'Mulyadi Suteja', 'Nurdin Abdullah', 'Oman Sukarman',
            'Pandi Siregar', 'Qomarudin', 'Rizal Ramli', 'Saeful Bahri', 'Tarmizi Taher',
        ];

        return $wbpNames[array_rand($wbpNames)];
    }

    private function getRandomRelationship(): string
    {
        $relationships = [
            'Keluarga' => 40,
            'Teman' => 20,
            'Saudara' => 25,
            'Pengacara' => 5,
            'Lainnya' => 10,
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($relationships as $relationship => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $relationship;
            }
        }

        return 'Keluarga';
    }
}
