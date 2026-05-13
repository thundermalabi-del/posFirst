<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $selectedCategory = '';
    public $showLowStock = false;
    public $showModal = false;
    public $modalType = 'in'; // 'in' or 'out' or 'adjustment'
    public $selectedProduct = null;
    public $quantity = 0;
    public $notes = '';

    // Import/Export properties
    public $importFile;
    public $showImportModal = false;
    public $importResults = [];
    public $showImportResults = false;
    public $isImporting = false;

    // Add Product Modal properties
    public $showAddProductModal = false;
    public $productName = '';
    public $productCode = '';
    public $productCategory = '';
    public $retailPrice = 0;
    public $wholesalePrice = 0;
    public $costPrice = 0;
    public $stockQuantity = 0;
    public $minStock = 5;
    public $unit = 'pcs';
    public $productDescription = '';

    // Add Category Modal properties
    public $showAddCategoryModal = false;

    // Edit Product Modal properties
    public $showEditProductModal = false;
    public $selectedEditProduct = null;
    public $editProductName = '';
    public $editRetailPrice = 0;
    public $editWholesalePrice = 0;
    public $editProductCategory = '';
    public $categoryName = '';
    public $categoryDescription = '';

    protected $paginationTheme = 'tailwind';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedShowLowStock()
    {
        $this->resetPage();
    }

    public function openStockModal($productId, $type)
    {
        $this->selectedProduct = Product::find($productId);
        $this->modalType = $type;
        $this->quantity = 0;
        $this->notes = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedProduct = null;
        $this->quantity = 0;
        $this->notes = '';
    }

    public function updateStock()
    {
        $this->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255'
        ]);

        if (!$this->selectedProduct) {
            session()->flash('error', 'Produk tidak ditemukan');
            return;
        }

        try {
            DB::beginTransaction();

            $product = $this->selectedProduct;
            $stockBefore = $product->stock_quantity;

            switch ($this->modalType) {
                case 'in':
                    $newStock = $stockBefore + $this->quantity;
                    break;
                case 'out':
                    if ($this->quantity > $stockBefore) {
                        session()->flash('error', 'Stok tidak mencukupi');
                        return;
                    }
                    $newStock = $stockBefore - $this->quantity;
                    break;
                case 'adjustment':
                    $newStock = $this->quantity;
                    $this->quantity = $newStock - $stockBefore; // For movement record
                    break;
            }

            // Update product stock
            $product->update(['stock_quantity' => $newStock]);

            // Create stock movement record
            StockMovement::create([
                'product_id' => $product->id,
                'type' => $this->modalType === 'adjustment' ? 'adjustment' : $this->modalType,
                'quantity' => abs($this->quantity),
                'stock_before' => $stockBefore,
                'stock_after' => $newStock,
                'reference_type' => 'manual',
                'notes' => $this->notes ?: 'Update manual oleh admin'
            ]);

            DB::commit();

            session()->flash('success', 'Stok berhasil diupdate');
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportStock()
    {
        $products = Product::with('category')->active()->get();

        $filename = 'stock_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($handle, [
                'Kode Produk',
                'Nama Produk',
                'Kategori',
                'Stok Saat Ini',
                'Stok Minimum',
                'Harga Retail',
                'Harga Grosir',
                'Unit',
                'Status'
            ]);

            // Data rows
            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->code,
                    $product->name,
                    $product->category->name ?? '',
                    $product->stock_quantity,
                    $product->min_stock,
                    $product->retail_price,
                    $product->wholesale_price,
                    $product->unit,
                    $product->is_active ? 'Aktif' : 'Tidak Aktif'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
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

    public function openImportModal()
    {
        $this->showImportModal = true;
        $this->importFile = null;
        $this->importResults = [];
        $this->showImportResults = false;
        $this->isImporting = false;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importResults = [];
        $this->showImportResults = false;
        $this->isImporting = false;
    }

    // Add Product Modal Functions
    public function openAddProductModal()
    {
        $this->showAddProductModal = true;
        $this->resetProductForm();
    }

    public function closeAddProductModal()
    {
        $this->showAddProductModal = false;
        $this->resetProductForm();
    }

    private function resetProductForm()
    {
        $this->productName = '';
        $this->productCode = '';
        $this->productCategory = '';
        $this->retailPrice = 0;
        $this->wholesalePrice = 0;
        $this->costPrice = 0;
        $this->stockQuantity = 0;
        $this->minStock = 5;
        $this->unit = 'pcs';
        $this->productDescription = '';
    }

    public function saveProduct()
    {
        $this->validate([
            'productName' => 'required|string|min:2|max:255',
            'productCategory' => 'required|exists:categories,id',
            'retailPrice' => 'required|numeric|min:0',
            'wholesalePrice' => 'required|numeric|min:0',
            'costPrice' => 'nullable|numeric|min:0',
            'stockQuantity' => 'required|integer|min:0',
            'minStock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'productDescription' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Generate product code if not provided
            $code = !empty($this->productCode) ? $this->productCode : $this->generateProductCode();

            // Check if code already exists
            if (Product::where('code', $code)->exists()) {
                session()->flash('error', 'Kode produk sudah digunakan');
                return;
            }

            // Create new product
            $product = Product::create([
                'code' => $code,
                'name' => $this->productName,
                'description' => $this->productDescription,
                'category_id' => $this->productCategory,
                'retail_price' => $this->retailPrice,
                'wholesale_price' => $this->wholesalePrice,
                'cost_price' => $this->costPrice ?: ($this->wholesalePrice * 0.8),
                'stock_quantity' => $this->stockQuantity,
                'min_stock' => $this->minStock,
                'unit' => $this->unit,
                'is_active' => true
            ]);

            // Create initial stock movement if stock > 0
            if ($this->stockQuantity > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $this->stockQuantity,
                    'stock_before' => 0,
                    'stock_after' => $this->stockQuantity,
                    'reference_type' => 'manual',
                    'notes' => 'Stok awal produk baru'
                ]);
            }

            DB::commit();

            session()->flash('success', "Produk '{$this->productName}' berhasil ditambahkan");
            $this->closeAddProductModal();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Add Category Modal Functions
    public function openAddCategoryModal()
    {
        $this->showAddCategoryModal = true;
        $this->resetCategoryForm();
    }

    public function closeAddCategoryModal()
    {
        $this->showAddCategoryModal = false;
        $this->resetCategoryForm();
    }

    private function resetCategoryForm()
    {
        $this->categoryName = '';
        $this->categoryDescription = '';
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|min:2|max:255|unique:categories,name',
            'categoryDescription' => 'nullable|string|max:500'
        ]);

        try {
            Category::create([
                'name' => $this->categoryName,
                'description' => $this->categoryDescription,
                'is_active' => true
            ]);

            session()->flash('success', "Kategori '{$this->categoryName}' berhasil ditambahkan");
            $this->closeAddCategoryModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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

    // Edit Product Modal Functions
    public function openEditProductModal($productId)
    {
        $product = Product::with('category')->find($productId);

        if (!$product) {
            session()->flash('error', 'Produk tidak ditemukan');
            return;
        }

        $this->selectedEditProduct = $product;
        $this->editProductName = $product->name;
        $this->editRetailPrice = $product->retail_price;
        $this->editWholesalePrice = $product->wholesale_price;
        $this->editProductCategory = $product->category_id;
        $this->showEditProductModal = true;
    }

    public function closeEditProductModal()
    {
        $this->showEditProductModal = false;
        $this->resetEditProductForm();
    }

    private function resetEditProductForm()
    {
        $this->selectedEditProduct = null;
        $this->editProductName = '';
        $this->editRetailPrice = 0;
        $this->editWholesalePrice = 0;
        $this->editProductCategory = '';
    }

    public function saveEditProduct()
    {
        if (!$this->selectedEditProduct) {
            session()->flash('error', 'Produk tidak ditemukan');
            return;
        }

        $this->validate([
            'editProductName' => 'required|string|min:2|max:255',
            'editProductCategory' => 'required|exists:categories,id',
            'editRetailPrice' => 'required|numeric|min:0',
            'editWholesalePrice' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Check if name already exists for different product
            $existingProduct = Product::where('name', $this->editProductName)
                ->where('id', '!=', $this->selectedEditProduct->id)
                ->first();

            if ($existingProduct) {
                session()->flash('error', 'Nama produk sudah digunakan produk lain');
                return;
            }

            // Update product
            $this->selectedEditProduct->update([
                'name' => $this->editProductName,
                'category_id' => $this->editProductCategory,
                'retail_price' => $this->editRetailPrice,
                'wholesale_price' => $this->editWholesalePrice,
            ]);

            DB::commit();

            session()->flash('success', "Produk '{$this->editProductName}' berhasil diupdate");
            $this->closeEditProductModal();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

    public function importStock()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        // Set loading state
        $this->isImporting = true;

        try {
            $path = $this->importFile->store('temp');
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
                        // Add quantity to existing product (tidak replace, tapi tambah)
                        $stockBefore = $product->stock_quantity;
                        $addedQuantity = (int) $stockQuantity;
                        $newStockQuantity = $stockBefore + $addedQuantity;

                        $product->update([
                            'name' => $productName, // Update nama jika berbeda
                            'category_id' => $category->id,
                            'retail_price' => $retailPrice,
                            'wholesale_price' => $wholesalePrice,
                            'stock_quantity' => $newStockQuantity, // Tambah, bukan ganti
                            'min_stock' => $minStock,
                            'unit' => $unit
                        ]);

                        // Create stock movement for added quantity
                        if ($addedQuantity > 0) {
                            StockMovement::create([
                                'product_id' => $product->id,
                                'type' => 'in', // Stok masuk karena menambah
                                'quantity' => $addedQuantity,
                                'stock_before' => $stockBefore,
                                'stock_after' => $newStockQuantity,
                                'reference_type' => 'import',
                                'notes' => $notes
                            ]);
                        }

                        $results['updated']++;
                        $results['success']++;
                        $results['details'][] = "Baris {$rowNumber}: Berhasil tambah stok '{$productName}' ({$product->code}) +{$addedQuantity} = {$newStockQuantity}";
                    } else {
                        // Create new product
                        $newCode = empty($productCode) ? $this->generateProductCode() : $productCode;

                        // Check if generated/provided code already exists
                        if (Product::where('code', $newCode)->exists()) {
                            $newCode = $this->generateProductCode();
                        }

                        $newProduct = Product::create([
                            'code' => $newCode,
                            'name' => $productName,
                            'category_id' => $category->id,
                            'retail_price' => $retailPrice,
                            'wholesale_price' => $wholesalePrice,
                            'cost_price' => $wholesalePrice * 0.8, // Estimate cost price
                            'stock_quantity' => $stockQuantity,
                            'min_stock' => $minStock,
                            'unit' => $unit,
                            'is_active' => true
                        ]);

                        // Create initial stock movement
                        if ($stockQuantity > 0) {
                            StockMovement::create([
                                'product_id' => $newProduct->id,
                                'type' => 'in',
                                'quantity' => $stockQuantity,
                                'stock_before' => 0,
                                'stock_after' => $stockQuantity,
                                'reference_type' => 'import',
                                'notes' => $notes
                            ]);
                        }

                        $results['created']++;
                        $results['success']++;
                        $results['details'][] = "Baris {$rowNumber}: Berhasil buat produk baru '{$productName}' dengan kode '{$newCode}'";
                    }
                }

                fclose($handle);
                DB::commit();

                // Clean up temp file
                Storage::delete($path);

                $this->importResults = $results;
                $this->showImportResults = true;

                if ($results['success'] > 0) {
                    session()->flash('success', "Import berhasil! {$results['created']} produk baru, {$results['updated']} produk diupdate, {$results['errors']} error.");
                }
            } else {
                throw new \Exception('Tidak dapat membaca file CSV');
            }
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        } finally {
            // Reset loading state
            $this->isImporting = false;
        }
    }

    public function render()
    {
        $query = Product::with('category')->active();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->showLowStock) {
            $query->lowStock();
        }

        $products = $query->paginate(10);

        // Load stock movements separately for each product to avoid MySQL window function issues
        $products->getCollection()->transform(function ($product) {
            $product->setRelation(
                'stockMovements',
                $product->stockMovements()->latest()->limit(5)->get()
            );
            return $product;
        });

        $categories = Category::active()->get();

        return view('livewire.stock-management', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
