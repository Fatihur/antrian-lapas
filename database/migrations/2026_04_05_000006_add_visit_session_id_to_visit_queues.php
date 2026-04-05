<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_queues', function (Blueprint $table) {
            $table->foreignId('visit_session_id')->nullable()->after('visit_schedule_id')->constrained('visit_sessions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('visit_queues', function (Blueprint $table) {
            $table->dropForeign(['visit_session_id']);
            $table->dropColumn('visit_session_id');
        });
    }
};
