<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'thinkpad' => [
                'name' => 'Lenovo ThinkPad',
                'description' => 'Lini laptop bisnis dengan bodi tangguh dan keyboard ergonomis.',
            ],
            'dell' => [
                'name' => 'Dell Latitude',
                'description' => 'Laptop bisnis Dell yang dirancang untuk kerja harian dan mudah dirawat.',
            ],
            'macbook' => [
                'name' => 'Apple MacBook',
                'description' => 'Laptop Apple dengan macOS dan desain aluminium premium.',
            ],
        ];

        foreach ($categories as $key => $category) {
            $categories[$key]['model'] = Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ]
            );
        }

        $products = [
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad T14 Gen 2 AMD',
                'processor' => 'AMD Ryzen 5 PRO 5650U (6C/12T)',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Health demo 92% (perkiraan 4-6 jam)',
                'physical_condition' => 'Demo: kondisi fisik 95%',
                'price' => 7500000,
                'stock' => 3,
                'description' => 'DATA DEMO — unit ilustrasi, harga, kondisi, dan stok bukan penawaran atau stok aktual.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad X1 Carbon Gen 7',
                'processor' => 'Intel Core i7-8665U',
                'ram_gb' => 16,
                'storage' => '256GB NVMe SSD',
                'battery_condition' => 'Health demo 86%',
                'physical_condition' => 'Demo: kondisi fisik 90%',
                'price' => 6800000,
                'stock' => 2,
                'description' => 'DATA DEMO — ultrabook bisnis ringan dengan layar FHD. Harga dan stok hanya ilustrasi.',
            ],
            [
                'category' => 'dell',
                'name' => 'Dell Latitude 7490',
                'processor' => 'Intel Core i5-8350U',
                'ram_gb' => 8,
                'storage' => '256GB SSD',
                'battery_condition' => 'Health demo 80% (perkiraan 3 jam)',
                'physical_condition' => 'Demo: kondisi fisik 88%',
                'price' => 3900000,
                'stock' => 4,
                'description' => 'DATA DEMO — laptop kerja dengan port lengkap. Harga dan stok hanya ilustrasi.',
            ],
            [
                'category' => 'macbook',
                'name' => 'MacBook Air M1 2020 Space Grey',
                'processor' => 'Apple M1 (8-core CPU)',
                'ram_gb' => 8,
                'storage' => '256GB SSD',
                'battery_condition' => 'Demo: 130 cycle count',
                'physical_condition' => 'Demo: kondisi fisik 97%',
                'price' => 9800000,
                'stock' => 1,
                'description' => 'DATA DEMO — MacBook Air M1. Kondisi, harga, dan stok tidak mewakili unit aktual.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad T480',
                'processor' => 'Intel Core i5-8350U',
                'ram_gb' => 8,
                'storage' => '256GB NVMe SSD',
                'battery_condition' => 'Demo: baterai internal dan eksternal',
                'physical_condition' => 'Demo: kondisi fisik 88%',
                'price' => 3500000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop bisnis 14 inci dengan RAM yang dapat di-upgrade. Harga dan stok ilustrasi.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad T490',
                'processor' => 'Intel Core i5-8365U',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 90%',
                'price' => 4500000,
                'stock' => 2,
                'description' => 'DATA DEMO — konfigurasi laptop bisnis untuk kerja kantor. Harga dan stok ilustrasi.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad T14 Gen 1 AMD',
                'processor' => 'AMD Ryzen 5 PRO 4650U (6C/12T)',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 90%',
                'price' => 5200000,
                'stock' => 2,
                'description' => 'DATA DEMO — ThinkPad 14 inci dengan prosesor Ryzen PRO. Harga dan stok ilustrasi.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad T14 Gen 1 Intel',
                'processor' => 'Intel Core i5-10310U',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 90%',
                'price' => 5400000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop bisnis 14 inci dengan layar FHD. Harga dan stok ilustrasi.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad T14 Gen 2 Intel',
                'processor' => 'Intel Core i5-1135G7',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 92%',
                'price' => 6400000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop kerja generasi ke-2 dengan prosesor Intel Core. Harga dan stok ilustrasi.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad X280',
                'processor' => 'Intel Core i5-8350U',
                'ram_gb' => 8,
                'storage' => '256GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 88%',
                'price' => 3600000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop bisnis ringkas 12,5 inci. Harga dan stok hanya ilustrasi.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad X1 Carbon Gen 6',
                'processor' => 'Intel Core i7-8650U',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 90%',
                'price' => 5700000,
                'stock' => 1,
                'description' => 'DATA DEMO — ultrabook bisnis berbahan serat karbon. Harga dan stok ilustrasi.',
            ],
            [
                'category' => 'thinkpad',
                'name' => 'ThinkPad X1 Carbon Gen 8',
                'processor' => 'Intel Core i5-10210U',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 92%',
                'price' => 7200000,
                'stock' => 1,
                'description' => 'DATA DEMO — ultrabook bisnis 14 inci yang ringan. Harga dan stok hanya ilustrasi.',
            ],
            [
                'category' => 'dell',
                'name' => 'Dell Latitude 7390',
                'processor' => 'Intel Core i5-8350U',
                'ram_gb' => 8,
                'storage' => '256GB SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 88%',
                'price' => 3600000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop bisnis ringkas 13 inci. Harga dan stok hanya ilustrasi.',
            ],
            [
                'category' => 'dell',
                'name' => 'Dell Latitude 7400',
                'processor' => 'Intel Core i5-8365U',
                'ram_gb' => 16,
                'storage' => '512GB SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 90%',
                'price' => 4600000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop bisnis premium 14 inci. Harga dan stok hanya ilustrasi.',
            ],
            [
                'category' => 'dell',
                'name' => 'Dell Latitude 7410',
                'processor' => 'Intel Core i5-10310U',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 90%',
                'price' => 5600000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop kerja 14 inci dengan konfigurasi RAM 16GB. Harga dan stok ilustrasi.',
            ],
            [
                'category' => 'dell',
                'name' => 'Dell Latitude 7420',
                'processor' => 'Intel Core i5-1145G7',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 92%',
                'price' => 6800000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop bisnis generasi lebih baru. Harga dan stok hanya ilustrasi.',
            ],
            [
                'category' => 'dell',
                'name' => 'Dell Latitude 5420',
                'processor' => 'Intel Core i5-1145G7',
                'ram_gb' => 16,
                'storage' => '512GB NVMe SSD',
                'battery_condition' => 'Demo: kondisi baterai normal',
                'physical_condition' => 'Demo: kondisi fisik 92%',
                'price' => 6500000,
                'stock' => 2,
                'description' => 'DATA DEMO — laptop kerja 14 inci dengan prosesor Intel Core generasi ke-11. Harga dan stok ilustrasi.',
            ],
            [
                'category' => 'macbook',
                'name' => 'MacBook Air M2 2022 Silver',
                'processor' => 'Apple M2 (8-core CPU)',
                'ram_gb' => 8,
                'storage' => '256GB SSD',
                'battery_condition' => 'Demo: battery health 90%',
                'physical_condition' => 'Demo: kondisi fisik 92%',
                'price' => 13500000,
                'stock' => 1,
                'description' => 'DATA DEMO — MacBook Air dengan chip M2. Kondisi, harga, dan stok hanya ilustrasi.',
            ],
            [
                'category' => 'macbook',
                'name' => 'MacBook Pro 13 M1 2020',
                'processor' => 'Apple M1 (8-core CPU)',
                'ram_gb' => 8,
                'storage' => '256GB SSD',
                'battery_condition' => 'Demo: battery health 90%',
                'physical_condition' => 'Demo: kondisi fisik 92%',
                'price' => 13800000,
                'stock' => 1,
                'description' => 'DATA DEMO — MacBook Pro 13 inci dengan chip M1. Kondisi, harga, dan stok ilustrasi.',
            ],
            [
                'category' => 'macbook',
                'name' => 'MacBook Pro 14 M1 Pro 2021',
                'processor' => 'Apple M1 Pro (8-core CPU)',
                'ram_gb' => 16,
                'storage' => '512GB SSD',
                'battery_condition' => 'Demo: battery health 90%',
                'physical_condition' => 'Demo: kondisi fisik 92%',
                'price' => 21500000,
                'stock' => 1,
                'description' => 'DATA DEMO — MacBook Pro 14 inci dengan chip M1 Pro. Kondisi, harga, dan stok ilustrasi.',
            ],
        ];

        foreach ($products as $product) {
            $category = $categories[$product['category']]['model'];
            unset($product['category']);

            Product::firstOrCreate(
                ['name' => $product['name']],
                ['category_id' => $category->id, ...$product]
            );
        }
    }
}
