<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_queue_id')->constrained('visit_queues')->onDelete('cascade');
            $table->string('nama_pengikut', 150);
            $table->string('nomor_identitas_pengikut', 25);
            $table->enum('jenis_kelamin_pengikut', ['Laki-laki', 'Perempuan']);
            $table->timestamps();

            $table->index('visit_queue_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_followers');
    }
};
