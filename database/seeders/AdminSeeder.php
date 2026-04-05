<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'nama' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@lapas-sumbawa.go.id',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
            'last_login_at' => null,
        ]);

        Admin::create([
            'nama' => 'Operator 1',
            'username' => 'operator1',
            'email' => 'operator1@lapas-sumbawa.go.id',
            'password' => Hash::make('password123'),
            'role' => 'operator',
            'is_active' => true,
            'last_login_at' => null,
        ]);

        $this->command->info('Default admin accounts created:');
        $this->command->info('Super Admin: username=admin, password=password123');
        $this->command->info('Operator: username=operator1, password=password123');
    }
}
