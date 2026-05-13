<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Stok</h1>
            <div class="flex items-center space-x-4">
                <a href="{{ route('pos.index') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    ← Kembali ke POS
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4 mb-4">
                <div class="flex-1">
                    <input type="text" wire:model.live="search" placeholder="Cari produk..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:w-48">
                    <select wire:model.live="selectedCategory"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="showLowStock"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Stok Menipis</span>
                    </label>
                </div>
            </div>

            <!-- Export/Import Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                <button wire:click="exportStock"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Export Stok
                </button>
                <button wire:click="openImportModal"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                        </path>
                    </svg>
                    Import Produk & Stok
                </button>
                <button wire:click="downloadTemplate"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Download Template
                </button>
                <div class="border-l border-gray-300 mx-2"></div>
                <button wire:click="openAddProductModal"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Produk
                </button>
                <button wire:click="openAddCategoryModal"
                    class="bg-indigo-500 hover:bg-indigo-600 px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Tambah Kategori
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr class="{{ $product->stock_quantity <= $product->min_stock ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $product->code }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span
                                            class="text-sm font-medium {{ $product->stock_quantity <= $product->min_stock ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $product->stock_quantity }} {{ $product->unit }}
                                        </span>
                                        @if ($product->stock_quantity <= $product->min_stock)
                                            <span
                                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Stok Menipis
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">Min: {{ $product->min_stock }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>Retail: Rp {{ number_format($product->retail_price, 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-500">Grosir: Rp
                                        {{ number_format($product->wholesale_price, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="openEditProductModal({{ $product->id }})"
                                            class="bg-green-500 hover:bg-green-600  px-3 py-1 rounded text-xs">
                                            Edit
                                        </button>
                                        <button wire:click="openStockModal({{ $product->id }}, 'in')"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                                            + Masuk
                                        </button>
                                        <button wire:click="openStockModal({{ $product->id }}, 'out')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                            - Keluar
                                        </button>
                                        <button wire:click="openStockModal({{ $product->id }}, 'adjustment')"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                            Sesuai
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 bg-gray-50 border-t">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Stock Update Modal -->
    @if ($showModal && $selectedProduct)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $modalType === 'in' ? 'Stok Masuk' : ($modalType === 'out' ? 'Stok Keluar' : 'Penyesuaian Stok') }}
                    </h3>

                    <div class="mb-4 p-3 bg-gray-50 rounded">
                        <div class="text-sm font-medium text-gray-900">{{ $selectedProduct->name }}</div>
                        <div class="text-sm text-gray-500">Stok saat ini: {{ $selectedProduct->stock_quantity }}
                            {{ $selectedProduct->unit }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $modalType === 'adjustment' ? 'Stok Baru' : 'Jumlah' }}
                        </label>
                        <input type="number" wire:model="quantity"
                            min="{{ $modalType === 'adjustment' ? '0' : '1' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan {{ $modalType === 'adjustment' ? 'stok baru' : 'jumlah' }}">
                        @error('quantity')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea wire:model="notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Catatan opsional..."></textarea>
                        @error('notes')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button wire:click="updateStock" class="px-4 py-2 bg-gray-500 rounded-lg hover:bg-gray-600">
                            Update Stok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Modal -->
    @if ($showImportModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <!-- Loading Overlay -->
                @if ($isImporting)
                    <div
                        class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-md">
                        <div class="text-center">
                            <svg class="animate-spin mx-auto h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Memproses import...</p>
                        </div>
                    </div>
                @endif

                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Import Produk & Stok</h3>

                    @if (!$showImportResults)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih File CSV
                            </label>
                            <input type="file" wire:model="importFile" accept=".csv,.txt"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                {{ $isImporting ? 'disabled' : '' }}>
                            @error('importFile')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800 mb-2"><strong>Format CSV:</strong></p>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li>• Kolom 1: kode_produk (kosong untuk auto-generate)</li>
                                <li>• Kolom 2: nama_produk (pencarian existing berdasarkan nama)</li>
                                <li>• Kolom 3: nama_kategori (auto-create jika belum ada)</li>
                                <li>• Kolom 4: harga_retail</li>
                                <li>• Kolom 5: harga_grosir</li>
                                <li>• Kolom 6: stok_quantity (MENAMBAH ke stok existing)</li>
                                <li>• Kolom 7: min_stock (opsional)</li>
                                <li>• Kolom 8: unit (opsional)</li>
                                <li>• Kolom 9: catatan (opsional)</li>
                            </ul>
                            <div class="mt-2 p-2 bg-yellow-50 rounded border-l-4 border-yellow-400">
                                <p class="text-xs text-yellow-800">
                                    <strong>Catatan:</strong> Jika produk sudah ada (berdasarkan nama), stok akan
                                    DITAMBAH, bukan diganti!
                                </p>
                            </div>
                            <p class="text-xs text-blue-600 mt-2">
                                <button wire:click="downloadTemplate" class="underline">Download template</button>
                                untuk contoh format.
                            </p>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button wire:click="closeImportModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ $isImporting ? 'disabled' : '' }}>
                                Batal
                            </button>
                            <button wire:click="importStock" wire:loading.attr="disabled" wire:target="importStock"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                {{ !$importFile || $isImporting ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="importStock">Import</span>
                                <span wire:loading wire:target="importStock" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    @else
                        <!-- Import Results -->
                        <div class="mb-4">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Import Selesai</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $importResults['created'] ?? 0 }} produk baru,
                                        {{ $importResults['updated'] ?? 0 }} diupdate, {{ $importResults['errors'] }}
                                        error
                                    </p>
                                </div>
                            </div>

                            @if (count($importResults['details']) > 0)
                                <div class="max-h-48 overflow-y-auto bg-gray-50 rounded-lg p-3">
                                    @foreach ($importResults['details'] as $detail)
                                        <div class="text-xs text-gray-700 mb-1">{{ $detail }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end">
                            <button wire:click="closeImportModal"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Tutup
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Add Product Modal -->
    @if ($showAddProductModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Produk Baru</h3>

                    <form wire:submit.prevent="saveProduct">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama Produk -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk *</label>
                                <input type="text" wire:model="productName"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="Masukkan nama produk">
                                @error('productName')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Kode Produk -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Produk</label>
                                <input type="text" wire:model="productCode"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="Auto-generate jika kosong">
                                @error('productCode')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                                <select wire:model="productCategory"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('productCategory')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Harga Retail -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Retail *</label>
                                <input type="number" wire:model="retailPrice" min="0" step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('retailPrice')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Harga Grosir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Grosir *</label>
                                <input type="number" wire:model="wholesalePrice" min="0" step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('wholesalePrice')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Harga Modal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Modal</label>
                                <input type="number" wire:model="costPrice" min="0" step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="Otomatis 80% dari grosir">
                                @error('costPrice')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Stok Awal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal *</label>
                                <input type="number" wire:model="stockQuantity" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('stockQuantity')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Stok Minimum -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stok Minimum *</label>
                                <input type="number" wire:model="minStock" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('minStock')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Unit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Satuan *</label>
                                <input type="text" wire:model="unit"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="pcs, kg, liter, dll">
                                @error('unit')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea wire:model="productDescription" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="Deskripsi produk (opsional)"></textarea>
                                @error('productDescription')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeAddProductModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-purple-500  rounded-lg hover:bg-purple-600">
                                Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Category Modal -->
    @if ($showAddCategoryModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Kategori Baru</h3>

                    <form wire:submit.prevent="saveCategory">
                        <!-- Nama Kategori -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori *</label>
                            <input type="text" wire:model="categoryName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Masukkan nama kategori">
                            @error('categoryName')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Deskripsi Kategori -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea wire:model="categoryDescription" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Deskripsi kategori (opsional)"></textarea>
                            @error('categoryDescription')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="closeAddCategoryModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-500  rounded-lg hover:bg-indigo-600">
                                Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Product Modal -->
    @if ($showEditProductModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Produk</h3>

                    <form wire:submit.prevent="saveEditProduct">
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Nama Produk -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk *</label>
                                <input type="text" wire:model="editProductName"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Masukkan nama produk">
                                @error('editProductName')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                                <select wire:model="editProductCategory"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('editProductCategory')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Harga Retail -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Retail *</label>
                                <input type="number" wire:model="editRetailPrice" min="0" step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                @error('editRetailPrice')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Harga Grosir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Grosir *</label>
                                <input type="number" wire:model="editWholesalePrice" min="0" step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                @error('editWholesalePrice')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($selectedEditProduct)
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-600">
                                        <strong>Kode:</strong> {{ $selectedEditProduct->code }}<br>
                                        <strong>Stok Saat Ini:</strong> {{ $selectedEditProduct->stock_quantity }}
                                        {{ $selectedEditProduct->unit }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeEditProductModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-orange-500  rounded-lg hover:bg-orange-600">
                                Update Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>

<script>
    // Auto-hide flash messages
    setTimeout(function() {
        const alerts = document.querySelectorAll('.fixed.top-4.right-4');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
</script>
