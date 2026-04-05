<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_sessions', function (Blueprint $table) {
            $table->dropColumn(['istirahat_mulai', 'istirahat_selesai']);
        });
    }

    public function down(): void
    {
        Schema::table('visit_sessions', function (Blueprint $table) {
            $table->time('istirahat_mulai')->nullable();
            $table->time('istirahat_selesai')->nullable();
        });
    }
};
