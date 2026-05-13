<div class="min-h-screen bg-gray-50" x-data="{
    isFullscreen: false,
    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                this.isFullscreen = true;
            });
        } else {
            document.exitFullscreen().then(() => {
                this.isFullscreen = false;
            });
        }
    }
}"
    @fullscreenchange.window="isFullscreen = !!document.fullscreenElement">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b px-4 py-3">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">POS Kasir</h1>
            <div class="flex items-center space-x-4">
                <!-- Fullscreen Toggle Button -->
                <button @click="toggleFullscreen()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center space-x-2 transition-colors">
                    <svg x-show="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                        </path>
                    </svg>
                    <svg x-show="isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 9V4.5M9 9H4.5M9 9L3.5 3.5M15 9V4.5M15 9h4.5M15 9l5.5-5.5M9 15v4.5M9 15H4.5M9 15l-5.5 5.5M15 15v4.5M15 15h4.5m0 0l5.5 5.5">
                        </path>
                    </svg>
                    <span x-text="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"></span>
                </button>

                <a href="{{ route('stock.index') }}"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    📦 Stok
                </a>
                <a href="{{ route('reports.invoice') }}"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    📊 Invoice
                </a>

                <a href="{{ route('store.settings') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    ⚙️ Pengaturan
                </a>

                <span class="text-sm text-gray-600">{{ now()->format('d M Y, H:i') }}</span>
                {{-- <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Logout
                </button> --}}
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
                            placeholder="Cari produk (nama, kode, barcode)..."
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
            <div class="bg-white rounded-lg shadow-sm p-4 flex-1 overflow-y-auto" style="height: calc(100vh - 200px);">
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

                                    <h3 class="font-medium text-sm text-gray-900 mb-1 line-clamp-2">{{ $product->name }}
                                    </h3>
                                    <p class="text-xs text-blue-600 mb-2">Stok: {{ $product->stock_quantity }}</p>

                                    <!-- Price Selection Buttons -->
                                    <div class="mb-3 space-y-1">
                                        <button @click="selectedPrice = 'retail'"
                                            :class="selectedPrice === 'retail' ? 'bg-green-600 text-white' :
                                                'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="w-full py-1 px-2 rounded text-xs font-medium transition-colors">
                                            Retail: Rp {{ number_format($product->retail_price, 0, ',', '.') }}
                                        </button>
                                        <button @click="selectedPrice = 'wholesale'"
                                            :class="selectedPrice === 'wholesale' ? 'bg-orange-600 text-white' :
                                                'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="w-full py-1 px-2 rounded text-xs font-medium transition-colors">
                                            Grosir: Rp {{ number_format($product->wholesale_price, 0, ',', '.') }}
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
                                        x-show="quantity <= {{ $product->stock_quantity }} && {{ $product->stock_quantity }} > 0"
                                        @click="$wire.addToCart({{ $product->id }}, selectedPrice, quantity)"
                                        class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-2 rounded text-xs font-medium transition-colors">
                                        Tambah
                                    </button>
                                    <button x-show="{{ $product->stock_quantity }} <= 0" disabled
                                        class="w-full bg-gray-400 cursor-not-allowed text-gray-200 py-2 px-2 rounded text-xs font-medium">
                                        Stok Habis
                                    </button>
                                    <button
                                        x-show="quantity > {{ $product->stock_quantity }} && {{ $product->stock_quantity }} > 0"
                                        disabled
                                        class="w-full bg-gray-400 cursor-not-allowed text-gray-200 py-2 px-2 rounded text-xs font-medium">
                                        Stok Kurang
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
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $product->barcode ?? '-' }}
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
                                                    :class="selectedPrice === 'retail' ? 'bg-green-600 text-white' :
                                                        'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="block w-full py-1 px-2 rounded text-xs font-medium transition-colors">
                                                    Retail: Rp {{ number_format($product->retail_price, 0, ',', '.') }}
                                                </button>
                                                <button @click="selectedPrice = 'wholesale'"
                                                    :class="selectedPrice === 'wholesale' ? 'bg-orange-600 text-white' :
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
                                                x-show="quantity <= {{ $product->stock_quantity }} && {{ $product->stock_quantity }} > 0"
                                                @click="$wire.addToCart({{ $product->id }}, selectedPrice, quantity)"
                                                class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-sm font-medium transition-colors">
                                                Tambah
                                            </button>
                                            <button x-show="{{ $product->stock_quantity }} <= 0" disabled
                                                class="bg-gray-400 cursor-not-allowed text-gray-200 py-2 px-4 rounded text-sm font-medium">
                                                Stok Habis
                                            </button>
                                            <button
                                                x-show="quantity > {{ $product->stock_quantity }} && {{ $product->stock_quantity }} > 0"
                                                disabled
                                                class="bg-gray-400 cursor-not-allowed text-gray-200 py-2 px-4 rounded text-sm font-medium">
                                                Stok Kurang
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
                        @if (count($cart) > 0)
                            <span
                                class="ml-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ count($cart) }}</span>
                        @endif
                    </h2>
                    @if (count($cart) > 0)
                        <button wire:click="clearCart"
                            class="text-red-500 hover:text-red-700 text-xs font-medium bg-red-100 hover:bg-red-200 px-2 py-1 rounded-lg">
                            🗑️
                        </button>
                    @endif
                </div>
            </div>

            <!-- Add Custom Product Form -->
            <div class="p-3 border-b border-gray-200 bg-yellow-50">
                <h3 class="text-sm font-medium text-gray-700 mb-2">🛍️ Produk Kustom</h3>

                <div class="space-y-2">
                    <input type="text" id="customProductName" wire:model="customProductName"
                        placeholder="Nama produk..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">

                    <input type="number" id="customProductPrice" wire:model="customProductPrice"
                        placeholder="Harga..." min="0"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">

                    <div class="flex items-center space-x-2">
                        <button type="button" wire:click="decrementCustomQuantity"
                            onclick="console.log('Decrement clicked')"
                            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                            -
                        </button>
                        <span class="w-8 text-center font-medium">{{ $customProductQuantity }}</span>
                        <button type="button" wire:click="incrementCustomQuantity"
                            onclick="console.log('Increment clicked')"
                            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                            +
                        </button>
                        <button type="button" wire:click="addCustomProduct"
                            class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition-colors bg-green-500 hover:bg-green-600 text-white">
                            <span wire:loading.remove wire:target="addCustomProduct">Tambah</span>
                            <span wire:loading wire:target="addCustomProduct">Loading...</span>
                        </button>
                    </div>


                </div>

            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-3">
                @if (count($cart) > 0)
                    <div class="space-y-3">
                        @foreach ($cart as $key => $item)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-medium text-sm text-gray-900">{{ $item['name'] }}</h4>
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
                                    <button wire:click="removeFromCart('{{ $key }}')"
                                        class="text-red-500 hover:text-red-700 text-sm">
                                        ✕
                                    </button>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                            -
                                        </button>
                                        <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                        <button
                                            wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-sm font-medium">
                                            +
                                        </button>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
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
                    💳 Checkout
                </h2>
            </div>

            @if (count($cart) > 0)
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <!-- Customer Info -->
                    <div>
                        <input type="text" wire:model="customerName" placeholder="Nama Pelanggan (opsional)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Discount & Tax -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Diskon</label>
                            <input type="number" wire:model.live="discount" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Pajak</label>
                            <input type="number" wire:model.live="tax" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if ($discount > 0)
                            <div class="flex justify-between text-red-600">
                                <span>Diskon:</span>
                                <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($tax > 0)
                            <div class="flex justify-between">
                                <span>Pajak:</span>
                                <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg border-t pt-2">
                            <span>Total:</span>
                            <span>Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                        <select wire:model="paymentMethod"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="cash">Tunai</option>
                            <option value="card">Kartu</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <div x-data="{
                        localPaidAmount: @entangle('paidAmount'),
                        autoUpdate: true
                    }"
                        @paid-amount-updated.window="if(autoUpdate) localPaidAmount = $event.detail.paidAmount">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Bayar</label>
                        <div class="flex gap-2 mb-2">
                            <input type="number" x-model="localPaidAmount" @focus="autoUpdate = false"
                                @blur="autoUpdate = true; $wire.set('paidAmount', localPaidAmount)" min="0"
                                step="1000"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button wire:click="setPaidAmountToTotal" type="button"
                                @click="localPaidAmount = {{ $this->total }}; autoUpdate = true"
                                class="px-3 py-2 bg-blue-200 hover:bg-blue-300 text-blue-700 rounded-lg text-sm font-medium">
                                Pas
                            </button>
                        </div>
                        <!-- Quick Payment Buttons -->
                        <div class="grid grid-cols-4 gap-1">
                            <button type="button"
                                @click="localPaidAmount = {{ ceil($this->total / 50000) * 50000 }}; autoUpdate = true; $wire.set('paidAmount', {{ ceil($this->total / 50000) * 50000 }})"
                                class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs">
                                {{ number_format(ceil($this->total / 50000) * 50000, 0, ',', '.') }}
                            </button>
                            <button type="button"
                                @click="localPaidAmount = {{ ceil($this->total / 100000) * 100000 }}; autoUpdate = true; $wire.set('paidAmount', {{ ceil($this->total / 100000) * 100000 }})"
                                class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs">
                                {{ number_format(ceil($this->total / 100000) * 100000, 0, ',', '.') }}
                            </button>
                            <button type="button"
                                @click="localPaidAmount = 50000; autoUpdate = true; $wire.set('paidAmount', 50000)"
                                class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs">
                                50K
                            </button>
                            <button type="button"
                                @click="localPaidAmount = 100000; autoUpdate = true; $wire.set('paidAmount', 100000)"
                                class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs">
                                100K
                            </button>
                        </div>
                    </div>

                    @if ($paidAmount > 0 && $paidAmount >= $this->total)
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Kembalian:</p>
                            <p class="text-xl font-bold text-green-600">
                                Rp {{ number_format($this->change, 0, ',', '.') }}
                            </p>
                        </div>
                    @endif

                    <!-- Checkout Button -->
                    <button wire:click="processTransaction"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold text-lg transition-colors"
                        {{ $paidAmount < $this->total ? 'disabled' : '' }}>
                        Proses Transaksi
                    </button>

                    <!-- Print Receipt Button (appears after transaction) -->
                    @if (session()->has('transaction_id'))
                        <div class="space-y-2 mt-2">
                            <button
                                onclick="printTransactionBT({{ session('transaction_id') }}); $wire.clearTransactionSession();"
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-semibold text-lg transition-colors flex items-center justify-center space-x-2">
                                <span>🖨️</span>
                                <span>Print Struk</span>
                            </button>
                            <button wire:click="clearTransactionSession"
                                class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium text-sm transition-colors">
                                Lewati Print
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="flex-1 flex items-center justify-center p-4">
                    <div class="text-center text-gray-500">
                        <div class="text-4xl mb-2">💳</div>
                        <p class="text-sm">Tambahkan produk ke keranjang<br>untuk melakukan checkout</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

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

                // Debug: show cleaned text in console
                console.log('Cleaned Receipt Text:', receiptText);

                // Send to RawBT
                const url = "rawbt:" + encodeURIComponent(receiptText);
                window.location.href = url;
                window.Livewire.find('{{ $_instance->getId() }}').call('resetCustomProductForm');

                setTimeout(() => {
                    window.location.reload();
                }, 100);
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
            .replace(/rawbt:\/\/print\?data=/gi, '') // Remove full RawBT URL
            .replace(/rawbt:/gi, '') // Remove rawbt protocol
            .replace(/\?data=/gi, '') // Remove ?data= parameter
            .replace(/print\?/gi, '') // Remove print? references
            .replace(/\/\/+/g, '') // Remove multiple slashes
            .replace(/^\s+|\s+$/gm, '') // Trim whitespace from each line
            .replace(/\n\s*\n/g, '\n') // Remove empty lines
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
Item               Qty      Harga        Total
================================================`;


        // Add items
        transaction.items.forEach(item => {
            const itemName = (item.product_name || item.custom_product_name || '');
            const qty = item.quantity;
            const unit = item.unit || 'pcs';
            const price = item.price;
            const subtotal = item.subtotal;

            // Format qty with unit
            const qtyWithUnit = `${qty} ${unit}`;

            // Function to break text into chunks of max 20 characters
            const breakTextIntoChunks = (text, maxLength) => {
                const chunks = [];
                let words = text.split(' ');
                let currentChunk = '';

                words.forEach(word => {
                    if ((currentChunk + word).length <= maxLength) {
                        currentChunk += (currentChunk ? ' ' : '') + word;
                    } else {
                        if (currentChunk) chunks.push(currentChunk);
                        currentChunk = word.length <= maxLength ? word : word.substring(0,
                            maxLength);
                    }
                });

                if (currentChunk) chunks.push(currentChunk);
                return chunks;
            };

            // Break item name into chunks if too long
            if (itemName.length > 16) {
                const nameChunks = breakTextIntoChunks(itemName, 16);
                // First line with qty, price, total
                const firstLine = nameChunks[0].padEnd(16) +
                    qtyWithUnit.padStart(8) +
                    formatRupiahShort(price).padStart(12) +
                    formatRupiahShort(subtotal).padStart(12);
                text += `\n${firstLine}`;

                // Additional lines for remaining name parts
                for (let i = 1; i < nameChunks.length; i++) {
                    text += `\n${nameChunks[i]}`;
                }
            } else {
                const itemLine = itemName.padEnd(16) +
                    qtyWithUnit.padStart(8) +
                    formatRupiahShort(price).padStart(12) +
                    formatRupiahShort(subtotal).padStart(12);
                text += `\n${itemLine}`;
            }
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

    // Alternative quick print function for testing
    function printQuickReceiptBT() {
        const text = `STRUK PENJUALAN
TOKO MAJU JAYA
Jl. Contoh No. 123, Kota
Telp: 0812-3456-7890
================================================
Test Receipt
${new Date().toLocaleString('id-ID')}
================================================
Barang A              1 x    10.000 =    10.000
Barang B              2 x     5.000 =    10.000
------------------------------------------------
Subtotal                               20.000
================================================
TOTAL:                             20.000
================================================
Bayar (TUNAI):                         20.000
Kembalian:                                  0
================================================
*** TERIMA KASIH ***
SELAMAT BERBELANJA KEMBALI
`;

        // Clean and send to RawBT
        const cleanText = cleanReceiptText(text);
        const url = "rawbt://print?data=" + encodeURIComponent(cleanText);
        window.location.href = url;
    }

    // Auto print after successful transaction (optional)
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for Livewire success events
        Livewire.on('transactionCompleted', function(transactionId) {
            // Show print option or auto print
            if (confirm('Transaksi berhasil! Apakah ingin mencetak struk?')) {
                printTransactionBT(transactionId);
            }
        });
    });
</script>
</div>
