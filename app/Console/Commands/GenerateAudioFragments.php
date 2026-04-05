<?php

namespace App\Console\Commands;

use App\Services\AudioFragmentService;
use Illuminate\Console\Command;

class GenerateAudioFragments extends Command
{
    protected $signature = 'audio:generate-fragments';

    protected $description = 'Generate audio fragments untuk panggilan antrian';

    public function handle(AudioFragmentService $service): int
    {
        $this->info('🔊 Membuat file audio fragment...');
        $this->info('');

        // Check Google Cloud credentials
        if (! env('GOOGLE_APPLICATION_CREDENTIALS')) {
            $this->error('❌ GOOGLE_APPLICATION_CREDENTIALS tidak diatur di .env');
            $this->info('');
            $this->info('Cara setup:');
            $this->info('1. Buat project di Google Cloud Console');
            $this->info('2. Enable Text-to-Speech API');
            $this->info('3. Download service account key (JSON)');
            $this->info('4. Tambahkan ke .env:');
            $this->info('   GOOGLE_APPLICATION_CREDENTIALS=/path/to/key.json');
            $this->info('');

            return 1;
        }

        // Check existing fragments
        $check = $service->checkFragments();

        if ($check['complete']) {
            $this->info('✅ Semua audio fragment sudah tersedia ('.$check['existing'].' files)');
            $this->info('');
            $this->info('List file:');
            foreach ($check['missing_files'] as $file) {
                $this->info("  - {$file}.mp3");
            }

            return 0;
        }

        $this->info('📁 Total fragment yang dibutuhkan: '.$check['total_required']);
        $this->info('✅ Sudah ada: '.$check['existing']);
        $this->info('📝 Perlu dibuat: '.$check['missing']);
        $this->info('');

        if ($check['missing'] > 0) {
            $this->info('File yang belum ada:');
            foreach ($check['missing_files'] as $file) {
                $this->info("  - {$file}.mp3");
            }
            $this->info('');
        }

        // Generate fragments
        $this->info('🎵 Mulai generate audio...');
        $this->info('');

        $results = $service->generateAllFragments();

        $created = 0;
        $exists = 0;
        $errors = 0;

        foreach ($results as $result) {
            $status = $result['status'];
            $filename = $result['filename'];

            if ($status === 'created') {
                $this->info("  ✅ Created: {$filename}.mp3");
                $created++;
            } elseif ($status === 'exists') {
                $this->line("  ℹ️  Exists: {$filename}.mp3");
                $exists++;
            } else {
                $this->error("  ❌ Error: {$filename}.mp3 - {$result['error']}");
                $errors++;
            }
        }

        $this->info('');
        $this->info('📊 Ringkasan:');
        $this->info("  ✅ Dibuat baru: {$created}");
        $this->info("  ℹ️  Sudah ada: {$exists}");
        $this->info("  ❌ Error: {$errors}");
        $this->info('');

        if ($errors === 0) {
            $this->info('🎉 Audio fragments berhasil dibuat!');
            $this->info('');
            $this->info('Lokasi file: storage/app/public/audio/fragments/');
            $this->info('');
            $this->info('Cara penggunaan:');
            $this->info('1. Jalankan: php artisan storage:link');
            $this->info('2. Buka halaman Panggil Antrian');
            $this->info('3. Klik "Panggil" - audio akan diputar otomatis');

            return 0;
        } else {
            $this->error('⚠️  Ada error saat membuat audio fragments');

            return 1;
        }
    }
}
