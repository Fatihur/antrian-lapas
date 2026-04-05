<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_schedule_id')->constrained('visit_schedules')->onDelete('restrict');
            $table->string('kode_booking', 50)->unique();
            $table->string('nomor_antrian', 30)->unique();
            $table->string('nik_pendaftar', 25);
            $table->enum('jenis_identitas', ['KTP', 'SIM', 'Paspor', 'KK', 'Lainnya'])->default('KTP');
            $table->string('nama_pengunjung', 150);
            $table->string('no_hp', 20);
            $table->string('hubungan_wbp', 100);
            $table->string('nama_wbp', 150);
            $table->string('foto_identitas', 255);
            $table->text('catatan')->nullable();
            $table->enum('status_antrian', [
                'Menunggu Verifikasi',
                'Disetujui',
                'Ditolak',
                'Menunggu Dipanggil',
                'Dipanggil',
                'Selesai',
                'Kedaluwarsa',
            ])->default('Menunggu Verifikasi');
            $table->string('pdf_path', 255)->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamp('waktu_daftar')->useCurrent();
            $table->timestamp('waktu_verifikasi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('waktu_selesai')->nullable();
            $table->timestamps();

            $table->index('kode_booking');
            $table->index('nomor_antrian');
            $table->index('nik_pendaftar');
            $table->index('status_antrian');
            $table->index('waktu_daftar');
            $table->index(['visit_schedule_id', 'status_antrian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_queues');
    }
};
