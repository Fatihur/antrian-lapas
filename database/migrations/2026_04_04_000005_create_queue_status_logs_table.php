<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_queue_id')->constrained('visit_queues')->onDelete('cascade');
            $table->enum('status_lama', [
                'Disetujui',
                'Menunggu Dipanggil',
                'Dipanggil',
                'Selesai',
                'Kedaluwarsa'
            ]);
            $table->enum('status_baru', [
                'Disetujui',
                'Menunggu Dipanggil',
                'Dipanggil',
                'Selesai',
                'Kedaluwarsa'
            ]);
            $table->text('keterangan')->nullable();
            $table->foreignId('changed_by')->constrained('admins')->onDelete('restrict');
            $table->timestamp('waktu_perubahan')->useCurrent();
            $table->timestamps();
            
            $table->index('visit_queue_id');
            $table->index('changed_by');
            $table->index('waktu_perubahan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_status_logs');
    }
};
