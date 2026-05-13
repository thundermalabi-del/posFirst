<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Laporan</h1>
            <div class="flex items-center space-x-4">
                <a href="{{ route('pos.index') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    ← Kembali ke POS
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Date Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <select wire:model.live="selectedPeriod"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="today">Hari Ini</option>
                        <option value="yesterday">Kemarin</option>
                        <option value="this_week">Minggu Ini</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                <div class="flex gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dari</label>
                        <input type="date" wire:model.live="dateFrom"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sampai</label>
                        <input type="date" wire:model.live="dateTo"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <button wire:click="setActiveTab('sales')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'sales' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        📊 Penjualan
                    </button>

                </nav>
            </div>

            <div class="p-6">
                @if ($activeTab === 'sales' && $salesData)
                    <!-- Sales Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $salesData['summary']['total_transactions'] }}</div>
                            <div class="text-sm text-gray-600">Total Transaksi</div>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ number_format($salesData['summary']['total_items'], 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-600">Total Item Terjual</div>
                        </div>

                    </div>

                    <!-- Sales Transactions -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Invoice</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pelanggan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pembayaran</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($salesData['transactions'] as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $transaction->invoice_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->customer_name ?: 'Walk-in Customer' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst($transaction->payment_method) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button wire:click="openEditModal({{ $transaction->id }})"
                                                    class="text-orange-600 hover:text-orange-900 font-medium">Edit</button>
                                                <button onclick="printTransactionBT({{ $transaction->id }})"
                                                    class="text-blue-600 hover:text-blue-900 font-medium">Print</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $salesData['transactions']->links() }}
                    </div>
                @elseif($activeTab === 'products')
                    <!-- Top Products -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rank</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produk</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Qty Terjual</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($topProducts as $index => $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium 
                                                {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-800' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->product->name ?? 'Produk Terhapus' }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->product->code ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($item->total_quantity, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($activeTab === 'stock')
                    <!-- Low Stock Products -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produk</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stok Saat Ini</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Min Stok</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($lowStockProducts as $product)
                                    <tr class="bg-red-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->code }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $product->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                            {{ $product->stock_quantity }} {{ $product->unit }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $product->min_stock }} {{ $product->unit }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $product->stock_quantity === 0 ? 'Habis' : 'Menipis' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Invoice Modal -->
    @if ($showEditModal && $editingTransaction)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @add-to-cart.window="$wire.addToEditCartWithQuantity($event.detail.productId, $event.detail.priceType, $event.detail.quantity)">
            <div class="relative min-h-screen bg-gray-50">
                <!-- Modal Header -->
                <div class="bg-white shadow-sm border-b px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl font-bold text-gray-900">
                            Edit Invoice - {{ $editingTransaction->invoice_number }}
                        </h1>
                        <div class="flex items-center space-x-4">
                            <button wire:click="closeEditModal"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                ✕ Tutup
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex h-screen">
                    <!-- Products Section (Left) -->
                    <div class="flex-1 p-4 overflow-hidden bg-gray-50">
                        <!-- Search and Filter -->
                        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-1">
                                    <input type="text" wire:model.live="search"
                                        placeholder="Cari produk (nama, kode)..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                                </div>
                                <div class="sm:w-48">
                                    <select wire:model.live="selectedCategory"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="bg-white rounded-lg shadow-sm p-4 flex-1 overflow-y-auto"
                            style="height: calc(100vh - 200px);">
                            <!-- View Toggle Header -->
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Produk</h3>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="toggleViewMode"
                                        class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors {{ $viewMode === 'grid' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        <span class="text-sm">Grid</span>
                                    </button>
                                    <button wire:click="toggleViewMode"
                                        class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors {{ $viewMode === 'list' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm">List</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Grid View -->
                            @if ($viewMode === 'grid')
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @foreach ($products as $product)
                                        <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow bg-white"
                                            x-data="{ selectedPrice: 'retail', quantity: 1 }">
                                            <div class="text-center">
                                                <h3 class="font-medium text-sm text-gray-900 mb-1 line-clamp-2">
                                                    {{ $product->name }}</h3>
                                                <p class="text-xs text-blue-600 mb-2">Stok:
                                                    {{ $product->stock_quantity }}</p>

                                                <!-- Price Selection Buttons -->
                                                <div class="mb-3 space-y-1">
                                                    <button @click="selectedPrice = 'retail'"
                                                        :class="selectedPrice === 'retail' ? 'bg-green-600 text-white' :
                                                            'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                        class="w-full py-1 px-2 rounded text-xs font-medium transition-colors">
                                                        Retail: Rp
                                                        {{ number_format($product->retail_price, 0, ',', '.') }}
                                                    </button>
                                                    <button @click="selectedPrice = 'wholesale'"
                                                        :class="selectedPrice === 'wholesale' ? 'bg-orange-600 text-white' :
                                                            'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                        class="w-full py-1 px-2 rounded text-xs font-medium transition-colors">
                                                        Grosir: Rp
                                                        {{ number_format($product->wholesale_price, 0, ',', '.') }}
                                                    </button>
                                                </div>

                                                <!-- Quantity Input -->
                                                <div class="mb-3">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <button @click="quantity = Math.max(1, quantity - 1)"
                                                            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                                            -
                                                        </button>
                                                        <input type="number" x-model="quantity" min="1"
                                                            :max="{{ $product->stock_quantity }}"
                                                            class="w-12 text-center border border-gray-300 rounded text-sm py-1">
                                                        <button
                                                            @click="quantity = Math.min({{ $product->stock_quantity }}, quantity + 1)"
                                                            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                                            +
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Add to Cart Button -->
                                                <button
                                                    @click="$dispatch('add-to-cart', { productId: {{ $product->id }}, priceType: selectedPrice, quantity: quantity })"
                                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-2 rounded text-xs font-medium transition-colors"
                                                    {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                                    Tambah
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- List View -->
                            @if ($viewMode === 'list')
                                <div class="overflow-x-auto">
                                    <table class="w-full table-auto">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Produk</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Stok</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Harga</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Qty</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($products as $product)
                                                <tr class="hover:bg-gray-50" x-data="{ selectedPrice: 'retail', quantity: 1 }">
                                                    <td class="px-4 py-4">
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $product->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $product->code }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4">
                                                        <span
                                                            class="text-sm text-blue-600 font-medium">{{ $product->stock_quantity }}</span>
                                                    </td>
                                                    <td class="px-4 py-4">
                                                        <div class="space-y-1">
                                                            <button @click="selectedPrice = 'retail'"
                                                                :class="selectedPrice === 'retail' ?
                                                                    'bg-green-600 text-white' :
                                                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                                class="block w-full py-1 px-2 rounded text-xs font-medium transition-colors">
                                                                Retail: Rp
                                                                {{ number_format($product->retail_price, 0, ',', '.') }}
                                                            </button>
                                                            <button @click="selectedPrice = 'wholesale'"
                                                                :class="selectedPrice === 'wholesale' ?
                                                                    'bg-orange-600 text-white' :
                                                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                                class="block w-full py-1 px-2 rounded text-xs font-medium transition-colors">
                                                                Grosir: Rp
                                                                {{ number_format($product->wholesale_price, 0, ',', '.') }}
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4">
                                                        <div class="flex items-center space-x-2">
                                                            <button @click="quantity = Math.max(1, quantity - 1)"
                                                                class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                                                -
                                                            </button>
                                                            <input type="number" x-model="quantity" min="1"
                                                                :max="{{ $product->stock_quantity }}"
                                                                class="w-12 text-center border border-gray-300 rounded text-sm py-1">
                                                            <button
                                                                @click="quantity = Math.min({{ $product->stock_quantity }}, quantity + 1)"
                                                                class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                                                +
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4">
                                                        <button
                                                            @click="$dispatch('add-to-cart', { productId: {{ $product->id }}, priceType: selectedPrice, quantity: quantity })"
                                                            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-sm font-medium transition-colors {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                            {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                                            Tambah
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>

                    <!-- Cart Section (Middle) -->
                    <div class="w-72 lg:w-80 bg-white shadow-xl border-l-2 border-gray-300 flex flex-col">
                        <!-- Cart Header -->
                        <div class="p-4 border-b-2 border-gray-200 bg-blue-50">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                    🛒 Keranjang
                                    @if (count($editCart) > 0)
                                        <span
                                            class="ml-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ count($editCart) }}</span>
                                    @endif
                                </h2>
                                @if (count($editCart) > 0)
                                    <button wire:click="clearEditCart"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium bg-red-100 hover:bg-red-200 px-2 py-1 rounded-lg">
                                        🗑️
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Add Custom Product Form -->
                        <div class="p-3 border-b border-gray-200 bg-yellow-50">
                            <div class="mt-3 space-y-2">
                                <input type="text" wire:model="customProductName" placeholder="Nama produk..."
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <input type="number" wire:model="customProductPrice" placeholder="Harga..."
                                    min="0" step="1000"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <div class="flex items-center space-x-2">
                                    <button
                                        wire:click="$set('customProductQuantity', Math.max(1, customProductQuantity - 1))"
                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                        -
                                    </button>
                                    <input type="number" wire:model="customProductQuantity" min="1"
                                        class="w-12 text-center border border-gray-300 rounded text-sm py-1">
                                    <button wire:click="$set('customProductQuantity', customProductQuantity + 1)"
                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                        +
                                    </button>
                                    <button wire:click.prevent="addCustomProductToEdit"
                                        class="flex-1 py-2 px-3 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Items -->
                        <div class="flex-1 overflow-y-auto p-3">
                            @if (count($editCart) > 0)
                                <div class="space-y-3">
                                    @foreach ($editCart as $key => $item)
                                        <div class="border border-gray-200 rounded-lg p-3">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <h4 class="font-medium text-sm text-gray-900">
                                                            {{ $item['name'] }}</h4>
                                                        @if (isset($item['is_custom']) && $item['is_custom'])
                                                            <span
                                                                class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Kustom</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-gray-500">
                                                        {{ ucfirst($item['price_type']) }} - Rp
                                                        {{ number_format($item['price'], 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <button wire:click="removeFromEditCart('{{ $key }}')"
                                                    class="text-red-500 hover:text-red-700 text-sm">
                                                    ✕
                                                </button>
                                            </div>

                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <button
                                                        wire:click="updateEditQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                                        -
                                                    </button>
                                                    <span
                                                        class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                                    <button
                                                        wire:click="updateEditQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                                        +
                                                    </button>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-gray-900">
                                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-8">
                                    <div class="text-4xl mb-2">🛒</div>
                                    <p>Keranjang kosong</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Checkout Section (Right) -->
                    <div class="w-72 lg:w-80 bg-white shadow-xl border-l-2 border-gray-300 flex flex-col">
                        <!-- Checkout Header -->
                        <div class="p-4 border-b-2 border-gray-200 bg-green-50">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                💳 Update Invoice
                            </h2>
                        </div>

                        @if (count($editCart) > 0)
                            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                                <!-- Customer Info -->
                                <div>
                                    <input type="text" wire:model="editCustomerName"
                                        placeholder="Nama Pelanggan (opsional)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Discount & Tax -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Diskon</label>
                                        <input type="number" wire:model.live="editDiscount" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Pajak</label>
                                        <input type="number" wire:model.live="editTax" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <!-- Totals -->
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span>Rp {{ number_format($this->editSubtotal, 0, ',', '.') }}</span>
                                    </div>
                                    @if ($editDiscount > 0)
                                        <div class="flex justify-between text-red-600">
                                            <span>Diskon:</span>
                                            <span>-Rp {{ number_format($editDiscount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    @if ($editTax > 0)
                                        <div class="flex justify-between">
                                            <span>Pajak:</span>
                                            <span>Rp {{ number_format($editTax, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                                        <span>Total:</span>
                                        <span>Rp {{ number_format($this->editTotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Payment -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode
                                        Pembayaran</label>
                                    <select wire:model="editPaymentMethod"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="cash">Tunai</option>
                                        <option value="card">Kartu</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>

                                <div x-data="{
                                    localEditPaidAmount: @entangle('editPaidAmount'),
                                    autoUpdate: true
                                }"
                                    @edit-paid-amount-updated.window="if(autoUpdate) localEditPaidAmount = $event.detail.editPaidAmount">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Bayar</label>
                                    <div class="flex gap-2 mb-2">
                                        <input type="number" x-model="localEditPaidAmount"
                                            @focus="autoUpdate = false"
                                            @blur="autoUpdate = true; $wire.set('editPaidAmount', localEditPaidAmount)"
                                            min="0" step="1000"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <button wire:click="setPaidAmountToEditTotal" type="button"
                                            @click="localEditPaidAmount = {{ $this->editTotal }}; autoUpdate = true"
                                            class="px-3 py-2 bg-blue-200 hover:bg-blue-300 text-blue-700 rounded-lg text-sm font-medium">
                                            Pas
                                        </button>
                                    </div>
                                    <!-- Quick Payment Buttons -->
                                    <div class="grid grid-cols-4 gap-1">
                                        <button type="button"
                                            @click="localEditPaidAmount = {{ ceil($this->editTotal / 50000) * 50000 }}; autoUpdate = true; $wire.set('editPaidAmount', {{ ceil($this->editTotal / 50000) * 50000 }})"
                                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs">
                                            {{ number_format(ceil($this->editTotal / 50000) * 50000, 0, ',', '.') }}
                                        </button>
                                        <button type="button"
                                            @click="localEditPaidAmount = {{ ceil($this->editTotal / 100000) * 100000 }}; autoUpdate = true; $wire.set('editPaidAmount', {{ ceil($this->editTotal / 100000) * 100000 }})"
                                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs">
                                            {{ number_format(ceil($this->editTotal / 100000) * 100000, 0, ',', '.') }}
                                        </button>
                                        <button type="button"
                                            @click="localEditPaidAmount = 50000; autoUpdate = true; $wire.set('editPaidAmount', 50000)"
                                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs">
                                            50K
                                        </button>
                                        <button type="button"
                                            @click="localEditPaidAmount = 100000; autoUpdate = true; $wire.set('editPaidAmount', 100000)"
                                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs">
                                            100K
                                        </button>
                                    </div>
                                </div>

                                @if ($editPaidAmount > 0 && $editPaidAmount >= $this->editTotal)
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">Kembalian:</p>
                                        <p class="text-xl font-bold text-green-600">
                                            Rp {{ number_format($this->editChange, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Update Button -->
                                <button wire:click="updateTransaction"
                                    class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-semibold text-lg transition-colors"
                                    {{ $editPaidAmount < $this->editTotal ? 'disabled' : '' }}>
                                    Update Transaksi
                                </button>
                            </div>
                        @else
                            <div class="flex-1 flex items-center justify-center p-4">
                                <div class="text-center text-gray-500">
                                    <div class="text-4xl mb-2">💳</div>
                                    <p class="text-sm">Tambahkan produk ke keranjang<br>untuk melakukan update</p>
                                </div>
                            </div>
                        @endif
                    </div>
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
    function printTransactionBT(transactionId) {
        // Get transaction data via AJAX
        fetch(`/api/transaction/${transactionId}`)
            .then(response => response.json())
            .then(transaction => {
                let receiptText = formatReceiptText(transaction);

                // Clean the text from any unwanted tags or URLs
                receiptText = cleanReceiptText(receiptText);

                // Send to RawBT
                const url = "rawbt://print?data=" + encodeURIComponent(receiptText);
                window.location.href = url;
            })
            .catch(error => {
                console.error('Error fetching transaction:', error);
                // Fallback: show alert
                alert('Gagal mengambil data transaksi');
            });
    }

    function cleanReceiptText(text) {
        // Remove any HTML tags, URLs, or unwanted characters
        return text
            .replace(/<[^>]*>/g, '') // Remove HTML tags
            .replace(/rawbt:\/\/print\?data=/gi, '') // Remove RawBT URL (case insensitive)
            .replace(/\/\/print\?data=/gi, '') // Remove print data URL
            .replace(/print\?data=/gi, '') // Remove remaining print data references
            .replace(/rawbt:/gi, '') // Remove rawbt protocol
            .replace(/data=/gi, '') // Remove data= parameter
            .replace(/\/\/+/g, '') // Remove multiple slashes
            .replace(/^[\s\n]+|[\s\n]+$/g, '') // Trim whitespace and newlines
            .trim();
    }

    function formatReceiptText(transaction) {
        const date = new Date(transaction.created_at).toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        // Get store settings from transaction data (will be added by API)
        const storeName = transaction.store_name || 'TOKO SAYA';
        const storeAddress = transaction.store_address || 'Jl. Contoh No. 123, Kota';
        const storePhone = transaction.store_phone || '0812-3456-7890';

        let text = `${storeName}
${storeAddress}
Telp: ${storePhone}
================================================

Invoice: ${transaction.invoice_number}
Tanggal: ${date}
Kasir: Admin
Pelanggan: ${transaction.customer_name || 'Walk-in Customer'}

================================================
Item                 Qty   Harga      Total
================================================`;

        // Add items
        transaction.items.forEach(item => {
            const itemName = (item.product_name || item.custom_product_name || '').substring(0, 12);
            const qty = item.quantity;
            const price = item.price;
            const subtotal = item.subtotal;

            // Format item line with proper spacing for 80mm thermal printer
            const itemLine = itemName.padEnd(18) +
                qty.toString().padStart(5) +
                formatRupiahShort(price).padStart(10) +
                formatRupiahShort(subtotal).padStart(10);
            text += `\n${itemLine}`;
        });

        text += `\n================================================`;

        // Add totals
        text += `\nSubtotal:              ${formatRupiahShort(transaction.subtotal).padStart(20)}`;

        if (transaction.discount_amount && transaction.discount_amount > 0) {
            text += `\nDiskon:               -${formatRupiahShort(transaction.discount_amount).padStart(20)}`;
        }

        if (transaction.tax_amount && transaction.tax_amount > 0) {
            text += `\nPajak:                 ${formatRupiahShort(transaction.tax_amount).padStart(20)}`;
        }

        text += `\n================================================`;
        text += `\nTOTAL:                 ${formatRupiahShort(transaction.total_amount).padStart(20)}`;
        text += `\n================================================`;
        text +=
            `\nBayar (${transaction.payment_method.toUpperCase()}):    ${formatRupiahShort(transaction.paid_amount).padStart(16)}`;
        text += `\nKembalian:             ${formatRupiahShort(transaction.change_amount).padStart(20)}`;
        text += `\n================================================`;
        text += `\n*** TERIMA KASIH ***`;
        text += `\n${new Date().toLocaleString('id-ID')}`;

        return text;
    }

    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function formatRupiahShort(amount) {
        return new Intl.NumberFormat('id-ID').format(amount);
    }

    // Alternative print function for custom receipt format
    function printCustomReceiptBT() {
        const text = `STRUK PENJUALAN
TOKO MAJU JAYA
Jl. Contoh No. 123, Kota
Telp: 0812-3456-7890
================================================
Barang A                              10.000
Barang B                               5.000
------------------------------------------------
Total                                 15.000
================================================
*** TERIMA KASIH ***
SELAMAT BERBELANJA KEMBALI
`;

        // Clean and send to RawBT
        const cleanText = cleanReceiptText(text);
        const url = "rawbt://print?data=" + encodeURIComponent(cleanText);
        window.location.href = url;
    }
</script>
