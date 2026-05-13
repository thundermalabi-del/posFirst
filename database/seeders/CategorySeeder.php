<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'description' => 'Produk makanan dan cemilan'],
            ['name' => 'Minuman', 'description' => 'Berbagai jenis minuman'],
            ['name' => 'Elektronik', 'description' => 'Peralatan elektronik'],
            ['name' => 'Alat Tulis', 'description' => 'Perlengkapan kantor dan sekolah'],
            ['name' => 'Perawatan', 'description' => 'Produk perawatan dan kebersihan'],
            ['name' => 'Rokok', 'description' => 'Produk tembakau'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
