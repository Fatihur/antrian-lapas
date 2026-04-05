<?php

namespace Database\Factories;

use App\Models\VisitFollower;
use App\Models\VisitQueue;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitFollowerFactory extends Factory
{
    protected $model = VisitFollower::class;

    public function definition(): array
    {
        return [
            'visit_queue_id' => VisitQueue::factory(),
            'nama_pengikut' => $this->faker->name(),
            'nomor_identitas_pengikut' => $this->faker->numerify('################'),
            'jenis_kelamin_pengikut' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
        ];
    }
}
