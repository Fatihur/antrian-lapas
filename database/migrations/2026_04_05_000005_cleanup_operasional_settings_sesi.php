<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operasional_settings', function (Blueprint $table) {
            // Drop sesi-specific columns if they exist
            $columns = [
                'jam_buka_pagi',
                'jam_tutup_pagi',
                'istirahat_mulai_pagi',
                'istirahat_selesai_pagi',
                'jam_buka_siang',
                'jam_tutup_siang',
                'istirahat_mulai_siang',
                'istirahat_selesai_siang',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('operasional_settings', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Rename kuota_per_sesi back to kuota_default if exists
            if (Schema::hasColumn('operasional_settings', 'kuota_per_sesi')) {
                // Keep it as is - sessions will have their own kuota_sesi
            }
        });
    }

    public function down(): void
    {
        // Not needed - we're moving forward with new architecture
    }
};
