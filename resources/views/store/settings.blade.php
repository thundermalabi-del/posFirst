<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Toko - POS System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('pos.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        ← Kembali ke POS
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">⚙️ Pengaturan Toko</h1>
                </div>
                <span class="text-sm text-gray-600">{{ now()->format('d M Y, H:i') }}</span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Informasi Toko</h2>
                    <p class="text-gray-600">Setting informasi toko yang akan muncul di struk dan laporan</p>
                </div>

                <livewire:store-settings />
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
