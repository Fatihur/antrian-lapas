<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('sesi', ['PAGI', 'SIANG']);
            $table->integer('kuota_maksimal')->default(50);
            $table->integer('kuota_terpakai')->default(0);
            $table->enum('status_jadwal', ['buka', 'tutup'])->default('buka');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['tanggal', 'sesi']);
            $table->index('tanggal');
            $table->index('status_jadwal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_schedules');
    }
};
