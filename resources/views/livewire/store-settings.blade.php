<div class="space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Store Settings Form -->
    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Store Name -->
        <div>
            <label for="storeName" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Toko
            </label>
            <input type="text" wire:model="storeName" id="storeName"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan nama toko...">
            @error('storeName')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Store Address -->
        <div>
            <label for="storeAddress" class="block text-sm font-medium text-gray-700 mb-2">
                Alamat Toko
            </label>
            <textarea wire:model="storeAddress" id="storeAddress" rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan alamat lengkap toko..."></textarea>
            @error('storeAddress')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Store Phone -->
        <div>
            <label for="storePhone" class="block text-sm font-medium text-gray-700 mb-2">
                Nomor Telepon
            </label>
            <input type="text" wire:model="storePhone" id="storePhone"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan nomor telepon...">
            @error('storePhone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Store Email -->
        <div>
            <label for="storeEmail" class="block text-sm font-medium text-gray-700 mb-2">
                Email Toko (Opsional)
            </label>
            <input type="email" wire:model="storeEmail" id="storeEmail"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan email toko...">
            @error('storeEmail')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Store Description -->
        <div>
            <label for="storeDescription" class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi Toko (Opsional)
            </label>
            <textarea wire:model="storeDescription" id="storeDescription" rows="2"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan deskripsi singkat toko..."></textarea>
            @error('storeDescription')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tax Rate -->
        <div>
            <label for="taxRate" class="block text-sm font-medium text-gray-700 mb-2">
                Tarif Pajak Default (%)
            </label>
            <input type="number" wire:model="taxRate" id="taxRate" min="0" max="100" step="0.01"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="0.00">
            @error('taxRate')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Tarif pajak yang akan digunakan secara default untuk transaksi</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                Informasi ini akan muncul di struk dan laporan
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('pos.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Preview Section -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Struk</h3>
        <div class="bg-gray-100 p-4 rounded-lg max-w-sm">
            <div class="text-center border-b-2 border-gray-400 pb-2 mb-2">
                <h4 class="font-bold text-lg">{{ $storeName ?: 'NAMA TOKO' }}</h4>
                <p class="text-xs text-gray-600">{{ $storeAddress ?: 'Alamat toko akan muncul di sini' }}</p>
                <p class="text-xs text-gray-600">Telp: {{ $storePhone ?: 'Nomor telepon' }}</p>
                @if ($storeEmail)
                    <p class="text-xs text-gray-600">Email: {{ $storeEmail }}</p>
                @endif
            </div>
            <div class="text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>Invoice:</span>
                    <span>INV-001</span>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal:</span>
                    <span>{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
