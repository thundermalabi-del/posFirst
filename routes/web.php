<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/pos');
});

Route::get('/pos', function () {
    return view('pos.index');
})->name('pos.index');

Route::get('/stock', function () {
    return view('stock.index');
})->name('stock.index');

Route::get('/invoice', function () {
    return view('reports.invoice');
})->name('reports.invoice');
Route::get('/reports', function () {
    return view('reports.index');
})->name('reports.index');
Route::get('/invoice/{id}/print', [App\Http\Controllers\InvoiceController::class, 'print'])->name('invoice.print');
Route::get('/invoice/{id}/download', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');

// API route for Bluetooth printing
Route::get('/api/transaction/{id}', [App\Http\Controllers\InvoiceController::class, 'getTransactionData'])->name('api.transaction');

Route::get('/store/settings', [App\Http\Controllers\StoreSettingsController::class, 'index'])->name('store.settings');

// Stock Management & Import Routes
Route::get('/stock-management', App\Livewire\StockManagement::class)->name('stock.management');
Route::get('/import-stock', [App\Http\Controllers\ImportStockController::class, 'show'])->name('import.stock');
Route::post('/import-stock', [App\Http\Controllers\ImportStockController::class, 'import'])->name('import.stock.process');
Route::get('/import-stock/template', [App\Http\Controllers\ImportStockController::class, 'downloadTemplate'])->name('import.stock.template');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
