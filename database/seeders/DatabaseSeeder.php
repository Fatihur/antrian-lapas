<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== E-Antrian Lapas Seeder ===');
        $this->command->info('');

        $this->call([
            AdminSeeder::class,
            VisitQueueSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✓ Seeding selesai!');
        $this->command->info('');
        $this->command->info('Login default:');
        $this->command->info('- Admin: admin / password123');
        $this->command->info('- Operator: operator1 / password123');
    }
}
