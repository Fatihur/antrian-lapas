<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operasional_settings', function (Blueprint $table) {
            // Rename existing columns to PAGI session
            $table->renameColumn('jam_buka', 'jam_buka_pagi');
            $table->renameColumn('jam_tutup', 'jam_tutup_pagi');
            $table->renameColumn('istirahat_mulai', 'istirahat_mulai_pagi');
            $table->renameColumn('istirahat_selesai', 'istirahat_selesai_pagi');

            // Add columns for SIANG session
            $table->time('jam_buka_siang')->nullable()->after('istirahat_selesai_pagi');
            $table->time('jam_tutup_siang')->nullable()->after('jam_buka_siang');
            $table->time('istirahat_mulai_siang')->nullable()->after('jam_tutup_siang');
            $table->time('istirahat_selesai_siang')->nullable()->after('istirahat_mulai_siang');

            // Rename kuota_harian to kuota_per_sesi
            $table->renameColumn('kuota_harian', 'kuota_per_sesi');
        });
    }

    public function down(): void
    {
        Schema::table('operasional_settings', function (Blueprint $table) {
            // Rename back
            $table->renameColumn('jam_buka_pagi', 'jam_buka');
            $table->renameColumn('jam_tutup_pagi', 'jam_tutup');
            $table->renameColumn('istirahat_mulai_pagi', 'istirahat_mulai');
            $table->renameColumn('istirahat_selesai_pagi', 'istirahat_selesai');

            // Drop siang columns
            $table->dropColumn([
                'jam_buka_siang',
                'jam_tutup_siang',
                'istirahat_mulai_siang',
                'istirahat_selesai_siang',
            ]);

            // Rename back
            $table->renameColumn('kuota_per_sesi', 'kuota_harian');
        });
    }
};
