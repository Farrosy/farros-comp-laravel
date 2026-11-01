<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo accounts are only created when they do not already exist.
        User::firstOrCreate(
            ['email' => 'admin@farroscomp.com'],
            [
                'name' => 'Admin Farros Comp',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Gudang Farros Comp, Cileungsi',
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@farroscomp.com'],
            [
                'name' => 'Budi Customer',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '089876543210',
                'address' => 'Jl. Angsana Raya No. 15, Cileungsi',
            ]
        );

        $this->call(ProductSeeder::class);
    }
}
