<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadAudioFragments extends Command
{
    protected $signature = 'audio:download';

    protected $description = 'Download audio fragments dari Google TTS (Gratis)';

    private $fragmentsPath = 'audio/fragments/';

    private $fragments = [
        'words' => [
            'perhatian' => 'Perhatian',
            'nomor-antrian' => 'Nomor antrian',
            'silakan-menuju' => 'Silakan menuju',
            'loket' => 'Loket',
            'panggilan-ulang' => 'Panggilan ulang',
            'ke' => 'ke',
        ],
        'numbers' => [
            0 => 'nol', 1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat',
            5 => 'lima', 6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan',
        ],
        'letters' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
        'counters' => [1, 2, 3, 4],
    ];

    public function handle(): int
    {
        $this->info('🔊 Download audio fragments dari Google TTS (GRATIS)...');
        $this->info('');

        // Create directory
        $fullPath = storage_path('app/public/'.$this->fragmentsPath);
        if (! is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        $downloaded = 0;
        $skipped = 0;
        $failed = 0;

        // Download words
        $this->info('📝 Download kata-kata...');
        foreach ($this->fragments['words'] as $filename => $text) {
            $result = $this->downloadAudio($filename, $text);
            if ($result === 'downloaded') {
                $downloaded++;
            } elseif ($result === 'skipped') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        // Download numbers
        $this->info('🔢 Download angka...');
        foreach ($this->fragments['numbers'] as $num => $text) {
            $result = $this->downloadAudio("num-{$num}", $text);
            if ($result === 'downloaded') {
                $downloaded++;
            } elseif ($result === 'skipped') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        // Download letters
        $this->info('🔤 Download huruf...');
        foreach ($this->fragments['letters'] as $letter) {
            $result = $this->downloadAudio("letter-{$letter}", $letter);
            if ($result === 'downloaded') {
                $downloaded++;
            } elseif ($result === 'skipped') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        // Download counters
        $this->info('🏢 Download nomor loket...');
        foreach ($this->fragments['counters'] as $counter) {
            $result = $this->downloadAudio("counter-{$counter}", (string) $counter);
            if ($result === 'downloaded') {
                $downloaded++;
            } elseif ($result === 'skipped') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        $this->info('');
        $this->info('📊 Ringkasan Download:');
        $this->info("  ✅ Downloaded: {$downloaded}");
        $this->info("  ⏭️  Skipped: {$skipped}");
        $this->info("  ❌ Failed: {$failed}");
        $this->info('');

        if ($failed === 0) {
            $this->info('🎉 Semua audio berhasil di-download!');
            $this->info('');
            $this->info('Lokasi: storage/app/public/audio/fragments/');
            $this->info('');
            $this->info('Langkah selanjutnya:');
            $this->info('1. php artisan storage:link');
            $this->info('2. Buka halaman Panggil Antrian');
            $this->info('3. Test panggilan dengan suara');

            return 0;
        } else {
            $this->warn('⚠️  Ada beberapa file yang gagal di-download');
            $this->info('Coba jalankan command lagi atau check koneksi internet');

            return 1;
        }
    }

    /**
     * Download audio dari Google Translate TTS (FREE)
     */
    private function downloadAudio(string $filename, string $text): string
    {
        $filepath = $this->fragmentsPath.$filename.'.mp3';
        $fullPath = storage_path('app/public/'.$filepath);

        // Check if already exists
        if (file_exists($fullPath)) {
            $this->line("  ℹ️  {$filename}.mp3 (exists)");

            return 'skipped';
        }

        try {
            // Google Translate TTS endpoint (FREE, no API key needed)
            $url = 'https://translate.google.com/translate_tts';
            $params = [
                'ie' => 'UTF-8',
                'q' => $text,
                'tl' => 'id', // Indonesian language
                'client' => 'tw-ob',
                'ttsspeed' => '0.5', // Slower for clarity
            ];

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])->timeout(30)->get($url, $params);

            if ($response->successful()) {
                Storage::disk('public')->put($filepath, $response->body());
                $this->info("  ✅ {$filename}.mp3 (downloaded)");

                return 'downloaded';
            } else {
                $this->error("  ❌ {$filename}.mp3 (HTTP {$response->status()})");

                return 'failed';
            }

        } catch (\Exception $e) {
            $this->error("  ❌ {$filename}.mp3 (Error: {$e->getMessage()})");

            return 'failed';
        }
    }
}
