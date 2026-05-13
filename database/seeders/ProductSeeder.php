<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $makanan = Category::where('name', 'Makanan')->first();
        $minuman = Category::where('name', 'Minuman')->first();
        $elektronik = Category::where('name', 'Elektronik')->first();
        $alatTulis = Category::where('name', 'Alat Tulis')->first();
        $perawatan = Category::where('name', 'Perawatan')->first();
        $rokok = Category::where('name', 'Rokok')->first();

        $products = [
            // Makanan
            ['code' => 'MKN001', 'name' => 'Indomie Goreng', 'category_id' => $makanan->id, 'retail_price' => 3500, 'wholesale_price' => 3000, 'cost_price' => 2500, 'stock_quantity' => 100, 'unit' => 'pcs'],
            ['code' => 'MKN002', 'name' => 'Roti Tawar', 'category_id' => $makanan->id, 'retail_price' => 8000, 'wholesale_price' => 7500, 'cost_price' => 6500, 'stock_quantity' => 50, 'unit' => 'pcs'],
            ['code' => 'MKN003', 'name' => 'Biskuit Marie', 'category_id' => $makanan->id, 'retail_price' => 5000, 'wholesale_price' => 4500, 'cost_price' => 3800, 'stock_quantity' => 75, 'unit' => 'pcs'],

            // Minuman
            ['code' => 'MNM001', 'name' => 'Aqua 600ml', 'category_id' => $minuman->id, 'retail_price' => 3000, 'wholesale_price' => 2500, 'cost_price' => 2000, 'stock_quantity' => 200, 'unit' => 'btl'],
            ['code' => 'MNM002', 'name' => 'Teh Botol Sosro', 'category_id' => $minuman->id, 'retail_price' => 4000, 'wholesale_price' => 3500, 'cost_price' => 3000, 'stock_quantity' => 80, 'unit' => 'btl'],
            ['code' => 'MNM003', 'name' => 'Coca Cola 330ml', 'category_id' => $minuman->id, 'retail_price' => 5000, 'wholesale_price' => 4500, 'cost_price' => 3800, 'stock_quantity' => 60, 'unit' => 'kaleng'],

            // Elektronik
            ['code' => 'ELK001', 'name' => 'Kabel USB', 'category_id' => $elektronik->id, 'retail_price' => 15000, 'wholesale_price' => 12000, 'cost_price' => 10000, 'stock_quantity' => 30, 'unit' => 'pcs'],
            ['code' => 'ELK002', 'name' => 'Adaptor 5V', 'category_id' => $elektronik->id, 'retail_price' => 25000, 'wholesale_price' => 20000, 'cost_price' => 18000, 'stock_quantity' => 20, 'unit' => 'pcs'],

            // Alat Tulis
            ['code' => 'ATL001', 'name' => 'Pulpen Standard', 'category_id' => $alatTulis->id, 'retail_price' => 2000, 'wholesale_price' => 1500, 'cost_price' => 1200, 'stock_quantity' => 150, 'unit' => 'pcs'],
            ['code' => 'ATL002', 'name' => 'Buku Tulis 58 Lembar', 'category_id' => $alatTulis->id, 'retail_price' => 4000, 'wholesale_price' => 3500, 'cost_price' => 3000, 'stock_quantity' => 80, 'unit' => 'pcs'],

            // Perawatan
            ['code' => 'PRW001', 'name' => 'Sabun Cuci Tangan', 'category_id' => $perawatan->id, 'retail_price' => 8000, 'wholesale_price' => 7000, 'cost_price' => 6000, 'stock_quantity' => 40, 'unit' => 'btl'],
            ['code' => 'PRW002', 'name' => 'Tissue Pocket', 'category_id' => $perawatan->id, 'retail_price' => 3000, 'wholesale_price' => 2500, 'cost_price' => 2000, 'stock_quantity' => 100, 'unit' => 'pcs'],

            // Rokok
            ['code' => 'RKK001', 'name' => 'Marlboro Merah', 'category_id' => $rokok->id, 'retail_price' => 25000, 'wholesale_price' => 23000, 'cost_price' => 21000, 'stock_quantity' => 50, 'unit' => 'bks'],
            ['code' => 'RKK002', 'name' => 'Gudang Garam Filter', 'category_id' => $rokok->id, 'retail_price' => 22000, 'wholesale_price' => 20000, 'cost_price' => 18500, 'stock_quantity' => 60, 'unit' => 'bks'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
