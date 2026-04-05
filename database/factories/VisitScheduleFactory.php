<?php

namespace Database\Factories;

use App\Models\VisitSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitScheduleFactory extends Factory
{
    protected $model = VisitSchedule::class;

    public function definition(): array
    {
        $tanggal = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $sesi = $this->faker->randomElement(['PAGI', 'SIANG']);

        return [
            'tanggal' => $tanggal,
            'sesi' => $sesi,
            'kuota_maksimal' => $this->faker->numberBetween(20, 100),
            'kuota_terpakai' => 0,
            'status_jadwal' => 'buka',
            'jam_mulai' => $sesi === 'PAGI' ? '08:00:00' : '13:00:00',
            'jam_selesai' => $sesi === 'PAGI' ? '12:00:00' : '16:00:00',
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_jadwal' => 'tutup',
        ]);
    }

    public function full(): static
    {
        return $this->state(fn (array $attributes) => [
            'kuota_terpakai' => $attributes['kuota_maksimal'],
        ]);
    }
}
