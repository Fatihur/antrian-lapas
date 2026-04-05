<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_queues', function (Blueprint $table) {
            // Add tanggal_kunjungan column
            $table->date('tanggal_kunjungan')->nullable()->after('visit_schedule_id');

            // Make visit_schedule_id nullable
            $table->foreignId('visit_schedule_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('visit_queues', function (Blueprint $table) {
            $table->dropColumn('tanggal_kunjungan');

            // Revert visit_schedule_id to not nullable
            $table->foreignId('visit_schedule_id')->nullable(false)->change();
        });
    }
};
