<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi', 50); // Contoh: "Pagi", "Siang", "Sore", "Malam"
            $table->string('kode_sesi', 20)->unique(); // Contoh: "PAGI", "SIANG", "SORE"
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->time('istirahat_mulai')->nullable();
            $table->time('istirahat_selesai')->nullable();
            $table->integer('kuota_sesi')->default(50);
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0); // Untuk ordering
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('kode_sesi');
            $table->index('is_active');
            $table->index('urutan');
        });

        // Insert default sessions
        DB::table('visit_sessions')->insert([
            [
                'nama_sesi' => 'Sesi Pagi',
                'kode_sesi' => 'PAGI',
                'jam_buka' => '08:00:00',
                'jam_tutup' => '12:00:00',
                'kuota_sesi' => 50,
                'is_active' => true,
                'urutan' => 1,
                'keterangan' => 'Kunjungan pagi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_sesi' => 'Sesi Siang',
                'kode_sesi' => 'SIANG',
                'jam_buka' => '13:00:00',
                'jam_tutup' => '16:00:00',
                'kuota_sesi' => 50,
                'is_active' => true,
                'urutan' => 2,
                'keterangan' => 'Kunjungan siang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_sessions');
    }
};
