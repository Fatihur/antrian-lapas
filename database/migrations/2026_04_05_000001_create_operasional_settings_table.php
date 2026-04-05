<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operasional_settings', function (Blueprint $table) {
            $table->id();
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->time('istirahat_mulai')->nullable();
            $table->time('istirahat_selesai')->nullable();
            $table->integer('kuota_harian')->default(50);
            $table->enum('status_default', ['buka', 'tutup'])->default('buka');
            $table->json('hari_libur_mingguan')->nullable(); // ["Minggu", "Sabtu"] dll
            $table->json('tanggal_libur_khusus')->nullable(); // ["2025-01-01", "2025-12-25"] dll
            $table->timestamps();
        });

        // Insert default settings
        DB::table('operasional_settings')->insert([
            'jam_buka' => '08:00:00',
            'jam_tutup' => '16:00:00',
            'istirahat_mulai' => '12:00:00',
            'istirahat_selesai' => '13:00:00',
            'kuota_harian' => 50,
            'status_default' => 'buka',
            'hari_libur_mingguan' => json_encode(['Minggu']),
            'tanggal_libur_khusus' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('operasional_settings');
    }
};
