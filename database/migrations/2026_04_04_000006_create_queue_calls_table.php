<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_queue_id')->constrained('visit_queues')->onDelete('cascade');
            $table->foreignId('called_by')->constrained('admins')->onDelete('restrict');
            $table->string('loket', 50)->nullable();
            $table->timestamp('waktu_panggilan')->useCurrent();
            $table->timestamp('waktu_selesai')->nullable();
            $table->enum('status_panggilan', ['Dipanggil', 'Selesai', 'Dilewati'])->default('Dipanggil');
            $table->unsignedTinyInteger('recall_count')->default(0);
            $table->timestamps();

            $table->index('visit_queue_id');
            $table->index('called_by');
            $table->index('waktu_panggilan');
            $table->index('loket');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_calls');
    }
};
