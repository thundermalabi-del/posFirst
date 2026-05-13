<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Stok Produk</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Import Stok Produk</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('stock.management') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Kembali ke Manajemen Stok
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Import Results -->
            @if (session('import_results'))
                @php $results = session('import_results'); @endphp
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Hasil Import</h2>

                        <!-- Summary Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $results['created'] }}</div>
                                <div class="text-xs text-green-800">Produk Baru</div>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $results['updated'] }}</div>
                                <div class="text-xs text-blue-800">Produk Diupdate</div>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $results['success'] }}</div>
                                <div class="text-xs text-green-800">Total Berhasil</div>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $results['errors'] }}</div>
                                <div class="text-xs text-red-800">Error</div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Results -->
                    @if (count($results['details']) > 0)
                        <div class="text-left mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Detail Import:</h3>
                            <div class="max-h-64 overflow-y-auto bg-gray-50 rounded-lg p-4 border">
                                @foreach ($results['details'] as $detail)
                                    <div
                                        class="text-xs text-gray-700 mb-1 py-1 px-2 @if (str_contains($detail, 'Error') || str_contains($detail, 'error')) bg-red-50 text-red-700 rounded @elseif(str_contains($detail, 'Berhasil')) bg-green-50 text-green-700 rounded @endif">
                                        {{ $detail }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Main Import Card -->
            <div class="bg-white rounded-lg shadow-sm p-8 max-w-2xl mx-auto">
                <div class="text-center mb-8">
                    <svg class="mx-auto h-16 w-16 text-blue-500 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Upload File CSV</h2>
                    <p class="text-gray-600">Pilih file CSV yang berisi data produk dan stok untuk diimport</p>
                </div>

                <!-- Import Form -->
                <form action="{{ route('import.stock.process') }}" method="POST" enctype="multipart/form-data"
                    id="importForm">
                    @csrf

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Pilih File CSV <span class="text-red-500">*</span>
                        </label>
                        <div
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200">
                            <input type="file" name="import_file" accept=".csv,.txt" id="file-upload" class="hidden"
                                required onchange="updateFileName(this)">
                            <label for="file-upload" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p class="text-gray-600" id="file-label">
                                    <span class="font-medium text-blue-600">Klik untuk pilih file</span>
                                    <br><span class="text-gray-500">atau drag & drop file CSV di sini</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">CSV, maksimal 2MB</p>
                            </label>
                        </div>
                        @error('import_file')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Format Info -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">Format File CSV:</h3>
                        <div class="text-xs text-blue-700 space-y-1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                                <div>• Kolom 1: kode_produk (opsional)</div>
                                <div>• Kolom 2: nama_produk *</div>
                                <div>• Kolom 3: nama_kategori *</div>
                                <div>• Kolom 4: harga_retail *</div>
                                <div>• Kolom 5: harga_grosir *</div>
                                <div>• Kolom 6: stok_quantity *</div>
                                <div>• Kolom 7: min_stock (opsional)</div>
                                <div>• Kolom 8: unit (opsional)</div>
                                <div class="sm:col-span-2">• Kolom 9: catatan (opsional)</div>
                            </div>
                        </div>

                        <div class="mt-3 p-3 bg-yellow-50 rounded border-l-4 border-yellow-400">
                            <p class="text-xs text-yellow-800">
                                <strong>Penting:</strong> Untuk produk yang sudah ada, stok akan
                                <strong>DITAMBAHKAN</strong>
                                ke stok yang sudah ada (bukan diganti). Kategori baru akan dibuat otomatis jika belum
                                ada.
                            </p>
                        </div>
                    </div>

                    <!-- Download Template Button -->
                    <div class="mb-6 text-center">
                        <a href="{{ route('import.stock.template') }}"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium underline inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Download Template CSV
                        </a>
                        <p class="text-xs text-gray-500 mt-1">File template dengan contoh format yang benar</p>
                    </div>

                    <!-- Import Button -->
                    <div class="text-center">
                        <button type="submit" id="submitBtn"
                            class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-8 py-3 rounded-lg text-lg font-medium transition-colors duration-200 inline-flex items-center justify-center">
                            <span id="btnText" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4">
                                    </path>
                                </svg>
                                Mulai Import
                            </span>
                            <span id="loadingText" class="hidden flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Memproses Import...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="max-w-2xl mx-auto mt-8">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bantuan</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-start">
                            <span class="font-medium text-gray-900 mr-2">•</span>
                            <span>File harus dalam format CSV dengan header di baris pertama</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-medium text-gray-900 mr-2">•</span>
                            <span>Maksimal ukuran file 2MB</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-medium text-gray-900 mr-2">•</span>
                            <span>Kode produk kosong akan di-generate otomatis</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-medium text-gray-900 mr-2">•</span>
                            <span>Kategori yang belum ada akan dibuat otomatis</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-medium text-gray-900 mr-2">•</span>
                            <span>Untuk produk yang sudah ada, stok akan ditambahkan (bukan diganti)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const label = document.getElementById('file-label');
            if (input.files && input.files[0]) {
                label.innerHTML = `<span class="font-medium text-blue-600">${input.files[0].name}</span>`;
            } else {
                label.innerHTML =
                    `<span class="font-medium text-blue-600">Klik untuk pilih file</span><br><span class="text-gray-500">atau drag & drop file CSV di sini</span>`;
            }
        }

        // Show loading state on form submit
        document.getElementById('importForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const loadingText = document.getElementById('loadingText');

            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            loadingText.classList.remove('hidden');
        });

        // Drag and drop functionality
        const dropZone = document.querySelector('input[type="file"]').parentElement;

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-blue-400', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('file-upload').files = files;
                updateFileName(document.getElementById('file-upload'));
            }
        });
    </script>
</body>

</html>
