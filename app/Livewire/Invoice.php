<?php

namespace App\Livewire;


use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionItem;
use App\Models\Category;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Invoice extends Component
{
    use WithPagination;

    public $activeTab = 'sales';
    public $dateFrom = '';
    public $dateTo = '';
    public $selectedPeriod = 'today';

    // Edit Modal Properties
    public $showEditModal = false;
    public $editingTransaction = null;
    public $editCart = [];
    public $editCustomerName = '';
    public $editDiscount = 0;
    public $editTax = 0;
    public $editPaymentMethod = 'cash';
    public $editPaidAmount = 0;

    // Product search properties for edit modal
    public $search = '';
    public $selectedCategory = '';
    public $viewMode = 'grid';

    // Custom product properties
    public $customProductName = '';
    public $customProductPrice = 0;
    public $customProductQuantity = 1;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->dateFrom = now()->startOfDay()->format('Y-m-d');
        $this->dateTo = now()->endOfDay()->format('Y-m-d');
    }

    public function updatedSelectedPeriod()
    {
        switch ($this->selectedPeriod) {
            case 'today':
                $this->dateFrom = now()->startOfDay()->format('Y-m-d');
                $this->dateTo = now()->endOfDay()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->dateFrom = now()->subDay()->startOfDay()->format('Y-m-d');
                $this->dateTo = now()->subDay()->endOfDay()->format('Y-m-d');
                break;
            case 'this_week':
                $this->dateFrom = now()->startOfWeek()->format('Y-m-d');
                $this->dateTo = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getSalesDataProperty()
    {
        $query = Transaction::with('items.product')
            ->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ])
            ->where('status', 'completed');

        return [
            'transactions' => $query->latest()->paginate(10),
            'summary' => [
                'total_transactions' => $query->count(),
                'total_revenue' => $query->sum('total_amount'),
                'total_items' => $query->withSum('items', 'quantity')->get()->sum('items_sum_quantity'),
                'avg_transaction' => $query->avg('total_amount') ?: 0,
            ]
        ];
    }

    public function getTopProductsProperty()
    {
        return TransactionItem::with('product')
            ->whereHas('transaction', function ($q) {
                $q->whereBetween('created_at', [
                    Carbon::parse($this->dateFrom)->startOfDay(),
                    Carbon::parse($this->dateTo)->endOfDay()
                ])->where('status', 'completed');
            })
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }

    public function getLowStockProductsProperty()
    {
        return Product::with('category')
            ->whereColumn('stock_quantity', '<=', 'min_stock')
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();
    }

    // Edit Modal Methods
    public function openEditModal($transactionId)
    {
        $this->editingTransaction = Transaction::with(['items.product', 'items.product.category'])->find($transactionId);

        if (!$this->editingTransaction) {
            session()->flash('error', 'Transaksi tidak ditemukan');
            return;
        }

        // Load transaction data into edit properties
        $this->editCustomerName = $this->editingTransaction->customer_name ?? '';
        $this->editDiscount = $this->editingTransaction->discount ?? 0;
        $this->editTax = $this->editingTransaction->tax ?? 0;
        $this->editPaymentMethod = $this->editingTransaction->payment_method ?? 'cash';
        $this->editPaidAmount = $this->editingTransaction->paid_amount ?? 0;

        // Load cart items
        $this->editCart = [];
        foreach ($this->editingTransaction->items as $item) {
            $key = $item->product_id ? 'product_' . $item->product_id . '_' . $item->price_type : 'custom_' . uniqid();
            $this->editCart[$key] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'price_type' => $item->price_type,
                'subtotal' => $item->subtotal,
                'is_custom' => !$item->product_id
            ];
        }

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTransaction = null;
        $this->editCart = [];
        $this->editCustomerName = '';
        $this->editDiscount = 0;
        $this->editTax = 0;
        $this->editPaymentMethod = 'cash';
        $this->editPaidAmount = 0;
        $this->search = '';
        $this->selectedCategory = '';
        $this->resetCustomProduct();
    }

    public function getEditProductsProperty()
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        return $query->paginate(12);
    }

    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)->get();
    }

    public function addToEditCart($productId, $priceType, $quantity = 1)
    {
        $product = Product::find($productId);
        if (!$product || $product->stock_quantity < $quantity) {
            session()->flash('error', 'Stok tidak mencukupi');
            return;
        }

        $price = $priceType === 'wholesale' ? $product->wholesale_price : $product->retail_price;
        $key = 'product_' . $productId . '_' . $priceType;

        if (isset($this->editCart[$key])) {
            $this->editCart[$key]['quantity'] += $quantity;
        } else {
            $this->editCart[$key] = [
                'product_id' => $productId,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'price_type' => $priceType,
                'is_custom' => false
            ];
        }

        $this->editCart[$key]['subtotal'] = $this->editCart[$key]['price'] * $this->editCart[$key]['quantity'];

        // Auto-update paid amount to new total
        $this->autoUpdateEditPaidAmount();
    }

    public function addToEditCartWithQuantity($productId, $priceType, $quantity)
    {
        $this->addToEditCart($productId, $priceType, $quantity);
    }

    public function updateEditQuantity($key, $newQuantity)
    {
        if ($newQuantity <= 0) {
            $this->removeFromEditCart($key);
            return;
        }

        if (isset($this->editCart[$key])) {
            // Check stock if it's not a custom product
            if (!$this->editCart[$key]['is_custom'] && $this->editCart[$key]['product_id']) {
                $product = Product::find($this->editCart[$key]['product_id']);
                if ($product && $product->stock_quantity < $newQuantity) {
                    session()->flash('error', 'Stok tidak mencukupi untuk ' . $this->editCart[$key]['name']);
                    return;
                }
            }

            $this->editCart[$key]['quantity'] = $newQuantity;
            $this->editCart[$key]['subtotal'] = $this->editCart[$key]['price'] * $newQuantity;
            $this->autoUpdateEditPaidAmount();
        }
    }

    public function removeFromEditCart($key)
    {
        unset($this->editCart[$key]);
        $this->autoUpdateEditPaidAmount();
    }

    public function clearEditCart()
    {
        $this->editCart = [];
        $this->editPaidAmount = 0;
    }

    public function addCustomProductToEdit()
    {
        if (empty($this->customProductName) || $this->customProductPrice <= 0) {
            session()->flash('error', 'Nama produk dan harga harus diisi');
            return;
        }

        $key = 'custom_' . uniqid();
        $this->editCart[$key] = [
            'product_id' => null,
            'name' => $this->customProductName,
            'price' => $this->customProductPrice,
            'quantity' => $this->customProductQuantity,
            'price_type' => 'custom',
            'is_custom' => true,
            'subtotal' => $this->customProductPrice * $this->customProductQuantity
        ];

        $this->resetCustomProduct();
        $this->autoUpdateEditPaidAmount();
    }

    private function resetCustomProduct()
    {
        $this->customProductName = '';
        $this->customProductPrice = 0;
        $this->customProductQuantity = 1;
    }

    public function getEditSubtotalProperty()
    {
        return array_sum(array_column($this->editCart, 'subtotal'));
    }

    public function getEditTotalProperty()
    {
        return $this->editSubtotal - $this->editDiscount + $this->editTax;
    }

    public function getEditChangeProperty()
    {
        return max(0, $this->editPaidAmount - $this->editTotal);
    }

    public function setPaidAmountToEditTotal()
    {
        $this->editPaidAmount = $this->editTotal;
    }

    public function setEditPaidAmount($amount)
    {
        $this->editPaidAmount = $amount;
    }

    public function autoUpdateEditPaidAmount()
    {
        // Auto-update paid amount to match new total if it was previously equal to the old total
        if ($this->editPaidAmount > 0) {
            $this->editPaidAmount = $this->editTotal;
            // Dispatch event to update Alpine.js
            $this->dispatch('edit-paid-amount-updated', editPaidAmount: $this->editPaidAmount);
        }
    }

    public function updatedEditDiscount()
    {
        $this->autoUpdateEditPaidAmount();
    }

    public function updatedEditTax()
    {
        $this->autoUpdateEditPaidAmount();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function updateTransaction()
    {
        if (empty($this->editCart)) {
            session()->flash('error', 'Keranjang tidak boleh kosong');
            return;
        }

        if ($this->editPaidAmount < $this->editTotal) {
            session()->flash('error', 'Jumlah bayar tidak mencukupi');
            return;
        }

        try {
            DB::beginTransaction();

            // Restore stock from original transaction
            foreach ($this->editingTransaction->items as $originalItem) {
                if ($originalItem->product) {
                    $originalItem->product->increment('stock_quantity', $originalItem->quantity);

                    // Create stock movement for restoration
                    StockMovement::create([
                        'product_id' => $originalItem->product_id,
                        'type' => 'in',
                        'quantity' => $originalItem->quantity,
                        'stock_before' => $originalItem->product->stock_quantity - $originalItem->quantity,
                        'stock_after' => $originalItem->product->stock_quantity,
                        'reference_type' => 'transaction_edit_restore',
                        'reference_id' => $this->editingTransaction->id,
                        'notes' => 'Restore stock from edited transaction'
                    ]);
                }
            }

            // Delete old transaction items
            $this->editingTransaction->items()->delete();

            // Deduct stock and add new items
            foreach ($this->editCart as $item) {
                if (!$item['is_custom'] && $item['product_id']) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->stock_quantity >= $item['quantity']) {
                        $product->decrement('stock_quantity', $item['quantity']);

                        // Create stock movement
                        StockMovement::create([
                            'product_id' => $item['product_id'],
                            'type' => 'out',
                            'quantity' => $item['quantity'],
                            'stock_before' => $product->stock_quantity + $item['quantity'],
                            'stock_after' => $product->stock_quantity,
                            'reference_type' => 'transaction_edit',
                            'reference_id' => $this->editingTransaction->id,
                            'notes' => 'Stock deduction from edited transaction'
                        ]);
                    } else {
                        throw new \Exception("Stok tidak mencukupi untuk produk: {$item['name']}");
                    }
                }

                // Create new transaction item
                TransactionItem::create([
                    'transaction_id' => $this->editingTransaction->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'price_type' => $item['price_type']
                ]);
            }

            // Update transaction
            $this->editingTransaction->update([
                'customer_name' => $this->editCustomerName ?: null,
                'subtotal' => $this->editSubtotal,
                'discount' => $this->editDiscount,
                'tax' => $this->editTax,
                'total_amount' => $this->editTotal,
                'payment_method' => $this->editPaymentMethod,
                'paid_amount' => $this->editPaidAmount,
                'change_amount' => $this->editChange,
            ]);

            DB::commit();

            session()->flash('success', 'Transaksi berhasil diupdate');
            $this->closeEditModal();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $data = [];

        if ($this->activeTab === 'sales') {
            $data = $this->salesData;
        }

        return view('livewire.invoice', [
            'salesData' => $this->activeTab === 'sales' ? $data : null,
            'topProducts' => $this->activeTab === 'products' ? $this->topProducts : collect(),
            'lowStockProducts' => $this->activeTab === 'stock' ? $this->lowStockProducts : collect(),
            'categories' => $this->categories,
            'products' => $this->showEditModal ? $this->editProducts : collect(),
        ]);
    }
}
