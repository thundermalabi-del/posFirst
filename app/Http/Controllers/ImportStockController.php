<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportStockController extends Controller
{
    public function show()
    {
        return view('import-stock.index');
    }

    public function downloadTemplate()
    {
        $filename = 'template_import_produk_stok.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($handle, [
                'kode_produk',
                'nama_produk',
                'nama_kategori',
                'harga_retail',
                'harga_grosir',
                'stok_quantity',
                'min_stock',
                'unit',
                'catatan'
            ]);

            // Sample data untuk produk baru (kode_produk kosong untuk auto-generate)
            fputcsv($handle, [
                '', // kosong untuk auto-generate
                'Mie Instan Pedas',
                'Makanan', // nama kategori (akan dibuat otomatis jika belum ada)
                '5000',
                '4500',
                '100',
                '10',
                'pcs',
                'Produk baru'
            ]);

            // Sample data untuk menambah stok produk existing (berdasarkan nama produk)
            fputcsv($handle, [
                '', // kode tidak wajib untuk update by name
                'Nasi Gudeg', // akan dicari berdasarkan nama ini
                'Makanan',
                '15000', // update harga
                '13000',
                '25', // TAMBAH stok sebanyak 25 (bukan ganti)
                '5',
                'porsi',
                'Tambah stok existing'
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('import_file');

            // Ensure temp directory exists
            if (!Storage::exists('temp')) {
                Storage::makeDirectory('temp');
            }

            $path = $file->store('temp');
            $fullPath = Storage::path($path);

            $results = [
                'success' => 0,
                'errors' => 0,
                'created' => 0,
                'updated' => 0,
                'details' => []
            ];

            if (($handle = fopen($fullPath, 'r')) !== false) {
                // Skip header row
                $header = fgetcsv($handle);
                $rowNumber = 1;

                DB::beginTransaction();

                while (($data = fgetcsv($handle)) !== false) {
                    $rowNumber++;

                    if (count($data) < 6) {
                        $results['errors']++;
                        $results['details'][] = "Baris {$rowNumber}: Format data tidak lengkap (minimal 6 kolom diperlukan)";
                        continue;
                    }

                    $productCode = trim($data[0]);
                    $productName = trim($data[1]);
                    $categoryName = trim($data[2]);
                    $retailPrice = trim($data[3]);
                    $wholesalePrice = trim($data[4]);
                    $stockQuantity = trim($data[5]);
                    $minStock = isset($data[6]) ? trim($data[6]) : 5;
                    $unit = isset($data[7]) ? trim($data[7]) : 'pcs';
                    $notes = isset($data[8]) ? trim($data[8]) : 'Import dari CSV';

                    // Validate essential data
                    if (
                        empty($productName) || empty($categoryName) || !is_numeric($retailPrice) ||
                        !is_numeric($wholesalePrice) || !is_numeric($stockQuantity) || $stockQuantity < 0
                    ) {
                        $results['errors']++;
                        $results['details'][] = "Baris {$rowNumber}: Data tidak valid (nama, kategori, harga, atau stok salah)";
                        continue;
                    }

                    // Find or create category by name
                    try {
                        $category = $this->findOrCreateCategory($categoryName);
                    } catch (\Exception $e) {
                        $results['errors']++;
                        $results['details'][] = "Baris {$rowNumber}: Error kategori - {$e->getMessage()}";
                        continue;
                    }

                    // Check if product exists by name or code
                    $product = null;

                    // First try to find by code if provided
                    if (!empty($productCode)) {
                        $product = Product::where('code', $productCode)->first();
                    }

                    // If not found by code, try to find by name (case insensitive)
                    if (!$product) {
                        $product = Product::whereRaw('LOWER(name) = ?', [strtolower($productName)])->first();
                    }

                    if ($product) {
                        // Update existing product
                        $stockBefore = $product->stock_quantity;
                        $addedQuantity = (int) $stockQuantity;
                        $newStockQuantity = $stockBefore + $addedQuantity;

                        // Update product details
                        $product->update([
                            'category_id' => $category->id,
                            'retail_price' => $retailPrice,
                            'wholesale_price' => $wholesalePrice,
                            'stock_quantity' => $newStockQuantity,
                            'min_stock' => $minStock,
                            'unit' => $unit,
                        ]);

                        // Create stock movement record for the addition
                        if ($addedQuantity > 0) {
                            StockMovement::create([
                                'product_id' => $product->id,
                                'type' => 'in',
                                'quantity' => $addedQuantity,
                                'stock_before' => $stockBefore,
                                'stock_after' => $newStockQuantity,
                                'reference_type' => 'csv_import',
                                'notes' => $notes
                            ]);
                        }

                        $results['success']++;
                        $results['updated']++;
                        $results['details'][] = "Baris {$rowNumber}: Berhasil tambah stok '{$productName}' ({$product->code}) +{$addedQuantity} = {$newStockQuantity}";
                    } else {
                        // Create new product
                        $code = !empty($productCode) ? $productCode : $this->generateProductCode();

                        // Check if generated code already exists
                        if (Product::where('code', $code)->exists()) {
                            $code = $this->generateProductCode(); // Generate another one
                        }

                        $newProduct = Product::create([
                            'code' => $code,
                            'name' => $productName,
                            'category_id' => $category->id,
                            'retail_price' => $retailPrice,
                            'wholesale_price' => $wholesalePrice,
                            'cost_price' => $wholesalePrice * 0.8, // Default cost price
                            'stock_quantity' => $stockQuantity,
                            'min_stock' => $minStock,
                            'unit' => $unit,
                            'is_active' => true
                        ]);

                        // Create initial stock movement if stock > 0
                        if ($stockQuantity > 0) {
                            StockMovement::create([
                                'product_id' => $newProduct->id,
                                'type' => 'in',
                                'quantity' => $stockQuantity,
                                'stock_before' => 0,
                                'stock_after' => $stockQuantity,
                                'reference_type' => 'csv_import',
                                'notes' => $notes
                            ]);
                        }

                        $results['success']++;
                        $results['created']++;
                        $results['details'][] = "Baris {$rowNumber}: Berhasil buat produk baru '{$productName}' ({$code}) dengan stok {$stockQuantity}";
                    }
                }

                fclose($handle);
                DB::commit();

                // Clean up temp file
                Storage::delete($path);

                if ($results['success'] > 0) {
                    $message = "Import berhasil! {$results['created']} produk baru, {$results['updated']} produk diupdate";
                    if ($results['errors'] > 0) {
                        $message .= ", {$results['errors']} error.";
                    }
                    return redirect()->back()->with('success', $message)->with('import_results', $results);
                } else {
                    return redirect()->back()->with('error', 'Import gagal. Tidak ada data yang berhasil diproses.')->with('import_results', $results);
                }
            } else {
                throw new \Exception('Tidak dapat membaca file CSV');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    private function findOrCreateCategory($categoryName)
    {
        $categoryName = trim($categoryName);

        if (empty($categoryName)) {
            throw new \Exception('Nama kategori tidak boleh kosong');
        }

        // Try to find existing category by name (case insensitive)
        $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();

        if ($category) {
            return $category;
        }

        // Create new category if not found
        $newCategory = Category::create([
            'name' => ucwords(strtolower($categoryName)), // Proper case formatting
            'description' => 'Auto-generated dari import CSV',
            'is_active' => true
        ]);

        return $newCategory;
    }

    private function generateProductCode()
    {
        // Get the last product code to increment
        $lastProduct = Product::orderBy('id', 'desc')->first();

        if (!$lastProduct || !$lastProduct->code) {
            return 'PRD001';
        }

        // Extract number from last code (assuming format PRDxxx)
        if (preg_match('/PRD(\d+)/', $lastProduct->code, $matches)) {
            $lastNumber = (int) $matches[1];
            $newNumber = $lastNumber + 1;
            return 'PRD' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        }

        // If format doesn't match, count all products and add 1
        $count = Product::count();
        return 'PRD' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}
