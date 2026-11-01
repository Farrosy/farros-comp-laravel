<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin & Customer Demo
        User::create([
            'name' => 'Admin Farros Comp',
            'email' => 'admin@farroscomp.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Gudang Farros Comp, Cileungsi',
        ]);

        User::create([
            'name' => 'Budi Customer',
            'email' => 'customer@farroscomp.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '089876543210',
            'address' => 'Jl. Angsana Raya No. 15, Cileungsi',
        ]);

        // Kategori Brand Laptop
        $thinkpad = Category::create([
            'name' => 'Lenovo ThinkPad',
            'slug' => Str::slug('Lenovo ThinkPad'),
            'description' => 'Lini laptop bisnis legendaris dengan bodi tangguh dan keyboard ergonomis.',
        ]);

        $dell = Category::create([
            'name' => 'Dell Latitude',
            'slug' => Str::slug('Dell Latitude'),
            'description' => 'Seri laptop kelas pekerja korporat andal dan mudah di-upgrade.',
        ]);

        $macbook = Category::create([
            'name' => 'Apple MacBook',
            'slug' => Str::slug('Apple MacBook'),
            'description' => 'Laptop premium efisien dengan layar retina tajam dan daya tahan baterai panjang.',
        ]);

        // Data Unit Laptop Second
        Product::create([
            'category_id' => $thinkpad->id,
            'name' => 'ThinkPad T14 Gen 2 AMD',
            'processor' => 'AMD Ryzen 5 PRO 5650U (6C/12T)',
            'ram_gb' => 16,
            'storage' => '512GB NVMe SSD',
            'battery_condition' => 'Health 92% (Tahan 4-6 jam)',
            'physical_condition' => 'Mulus 95% (Pemakaian wajar kantor)',
            'price' => 7500000,
            'stock' => 3,
            'description' => 'Ex-kantor luar negeri, keyboard backlite empuk US layout, layar FHD IPS jernih tanpa white spot.',
        ]);

        Product::create([
            'category_id' => $thinkpad->id,
            'name' => 'ThinkPad X1 Carbon Gen 7',
            'processor' => 'Intel Core i7-8665U',
            'ram_gb' => 16,
            'storage' => '256GB NVMe SSD',
            'battery_condition' => 'Health 86% (Normal)',
            'physical_condition' => 'Mulus 90%',
            'price' => 6800000,
            'stock' => 2,
            'description' => 'Ultrabook tipis berbobot 1.09 kg berbahan serat karbon premium, speaker Dolby Atmos mantap.',
        ]);

        Product::create([
            'category_id' => $dell->id,
            'name' => 'Dell Latitude 7490',
            'processor' => 'Intel Core i5-8350U',
            'ram_gb' => 8,
            'storage' => '256GB SSD',
            'battery_condition' => 'Health 80% (Tahan 3 jam)',
            'physical_condition' => 'Mulus 88%',
            'price' => 3900000,
            'stock' => 4,
            'description' => 'Laptop kerja harian yang kokoh, engsel 180 derajat, port lengkap (Thunderbolt 3, Type-C, HDMI, LAN).',
        ]);

        Product::create([
            'category_id' => $macbook->id,
            'name' => 'MacBook Air M1 2020 Space Grey',
            'processor' => 'Apple Silicon M1 (8-Core)',
            'ram_gb' => 8,
            'storage' => '256GB SSD',
            'battery_condition' => 'Normal Cycle Count 130',
            'physical_condition' => 'Mulus 97% Fullset Dus',
            'price' => 9800000,
            'stock' => 1,
            'description' => 'Bodi aluminium mulus tanpa dent, layar True Tone normal, iCloud kosong bebas reset factory.',
        ]);
    }
}
