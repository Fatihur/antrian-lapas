<?php

namespace App\Console\Commands;

use App\Models\VisitSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSchedules extends Command
{
    protected $signature = 'schedule:generate
                            {--start= : Tanggal mulai (Y-m-d)}
                            {--end= : Tanggal selesai (Y-m-d)}
                            {--weekdays-only : Hanya hari kerja (Senin-Jumat)}
                            {--exclude-holidays : Hindari tanggal merah}
                            {--default-quota=50 : Kuota default per sesi}
                            {--dry-run : Simulasi tanpa menyimpan}';

    protected $description = 'Generate jadwal kunjungan secara otomatis untuk rentang tanggal tertentu';

    private array $holidays = [
        '2025-01-01', // Tahun Baru
        '2025-01-27', // Isra Mi'raj
        '2025-03-29', // Hari Raya Nyepi
        '2025-04-18', // Wafat Isa Almasih
        '2025-04-20', // Hari Paskah
        '2025-05-01', // Hari Buruh
        '2025-05-12', // Kenaikan Isa Almasih
        '2025-05-29', // Hari Raya Waisak
        '2025-06-01', // Hari Lahir Pancasila
        '2025-06-07', // Idul Fitri
        '2025-06-08', // Idul Fitri
        '2025-08-17', // Hari Kemerdekaan
        '2025-09-05', // Maulid Nabi
        '2025-12-25', // Hari Raya Natal
    ];

    public function handle()
    {
        $startDate = $this->option('start') ?: now()->format('Y-m-d');
        $endDate = $this->option('end') ?: now()->addMonth()->format('Y-m-d');
        $weekdaysOnly = $this->option('weekdays-only');
        $excludeHolidays = $this->option('exclude-holidays');
        $defaultQuota = (int) $this->option('default-quota');
        $dryRun = $this->option('dry-run');

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->isAfter($end)) {
            $this->error('Tanggal mulai harus sebelum tanggal selesai!');
            return 1;
        }

        $this->info("Generate jadwal dari {$start->format('d M Y')} sampai {$end->format('d M Y')}");
        
        if ($weekdaysOnly) {
            $this->info('Mode: Hanya hari kerja (Senin-Jumat)');
        }
        
        if ($excludeHolidays) {
            $this->info('Mode: Melewati tanggal merah');
        }

        if ($dryRun) {
            $this->warn('DRY RUN MODE - Tidak ada data yang akan disimpan');
        }

        $generatedCount = 0;
        $skippedCount = 0;
        $bar = $this->output->createProgressBar($start->diffInDays($end) + 1);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $bar->advance();

            // Skip weekend jika weekdays-only
            if ($weekdaysOnly && ($date->isSaturday() || $date->isSunday())) {
                $skippedCount++;
                continue;
            }

            // Skip holidays jika exclude-holidays
            if ($excludeHolidays && in_array($date->format('Y-m-d'), $this->holidays)) {
                $skippedCount++;
                continue;
            }

            // Generate untuk 2 sesi: PAGI dan SIANG
            foreach (['PAGI', 'SIANG'] as $sesi) {
                $exists = VisitSchedule::where('tanggal', $date->format('Y-m-d'))
                    ->where('sesi', $sesi)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                if (!$dryRun) {
                    $jamMulai = $sesi === 'PAGI' ? '08:00' : '13:00';
                    $jamSelesai = $sesi === 'PAGI' ? '12:00' : '16:00';

                    VisitSchedule::create([
                        'tanggal' => $date->format('Y-m-d'),
                        'sesi' => $sesi,
                        'kuota_maksimal' => $defaultQuota,
                        'kuota_terpakai' => 0,
                        'status_jadwal' => 'buka',
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'keterangan' => 'Auto-generated',
                    ]);
                }

                $generatedCount++;
            }
        }

        $bar->finish();
        $this->newLine();

        if ($dryRun) {
            $this->info("Simulasi selesai. {$generatedCount} jadwal akan dibuat, {$skippedCount} dilewati.");
        } else {
            $this->info("✅ Berhasil! {$generatedCount} jadwal dibuat, {$skippedCount} dilewati.");
        }

        return 0;
    }
}
