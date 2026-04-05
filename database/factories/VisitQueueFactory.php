<?php

namespace Database\Factories;

use App\Models\VisitQueue;
use App\Models\VisitSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VisitQueueFactory extends Factory
{
    protected $model = VisitQueue::class;

    public function definition(): array
    {
        $tanggal = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $tanggalFormatted = $tanggal->format('dmY');
        $sesi = $this->faker->randomElement(['PAGI', 'SIANG']);
        $prefix = $this->faker->randomElement(['A', 'B', 'C', 'D']);
        $nomor = str_pad($this->faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        return [
            'visit_schedule_id' => VisitSchedule::factory(),
            'kode_booking' => strtoupper(Str::random(8)),
            'nomor_antrian' => "{$prefix}{$nomor}-{$sesi}-{$tanggalFormatted}",
            'nik_pendaftar' => $this->faker->numerify('################'),
            'jenis_identitas' => $this->faker->randomElement(['KTP', 'SIM', 'Paspor', 'KK', 'Lainnya']),
            'nama_pengunjung' => $this->faker->name(),
            'no_hp' => $this->faker->phoneNumber(),
            'hubungan_wbp' => $this->faker->randomElement(['Keluarga', 'Teman', 'Pengacara', 'Lainnya']),
            'nama_wbp' => $this->faker->name(),
            'foto_identitas' => 'identitas/'.$this->faker->uuid().'.jpg',
            'catatan' => $this->faker->optional()->sentence(),
            'status_antrian' => 'Disetujui',
            'pdf_path' => null,
            'alasan_penolakan' => null,
            'waktu_daftar' => now(),
            'waktu_verifikasi' => null,
            'verified_by' => null,
            'waktu_selesai' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_antrian' => 'Disetujui',
            'waktu_verifikasi' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_antrian' => 'Ditolak',
            'alasan_penolakan' => 'Data tidak valid',
            'waktu_verifikasi' => now(),
        ]);
    }

    public function called(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_antrian' => 'Dipanggil',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_antrian' => 'Selesai',
            'waktu_selesai' => now(),
        ]);
    }
}
