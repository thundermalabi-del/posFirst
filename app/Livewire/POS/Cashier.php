<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use App\Models\CartItem;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Cashier extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $viewMode = 'grid'; // 'grid' or 'list'
    public $cart = [];
    public $customerName = '';
    public $paymentMethod = 'cash';
    public $paidAmount = 0;
    public $discount = 0;
    public $tax = 0;

    // Custom product properties
    public $customProductName = '';
    public $customProductPrice = 0;
    public $customProductQuantity = 1;

    protected $rules = [
        'customProductName' => 'required|string|min:2|max:255',
        'customProductPrice' => 'required|numeric|min:0.01',
        'customProductQuantity' => 'required|integer|min:1|max:999',
    ];

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->loadCartFromDatabase();
    }

    public function hydrate()
    {
        // Auto-update paid amount on every request
        if (!empty($this->cart) && $this->paidAmount == 0) {
            $this->paidAmount = $this->getTotalProperty();
        }
    }

    private function loadCartFromDatabase()
    {
        $sessionId = session()->getId();
        $cartItems = CartItem::getCartBySession($sessionId);

        $this->cart = [];
        foreach ($cartItems as $item) {
            $key = $item->product_id ? $item->product_id . '_' . $item->price_type : 'custom_' . $item->id;
            $this->cart[$key] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'price' => $item->price,
                'price_type' => $item->price_type,
                'quantity' => $item->quantity,
                'is_custom' => $item->is_custom,
                'stock_available' => $item->product ? $item->product->stock_quantity : null,
            ];
        }

        $this->calculateCartTotals();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function addCustomProduct()
    {
        try {
            Log::info('addCustomProduct called', [
                'name' => $this->customProductName,
                'price' => $this->customProductPrice,
                'quantity' => $this->customProductQuantity
            ]);

            // Basic validation
            if (empty(trim($this->customProductName))) {
                session()->flash('error', 'Nama produk tidak boleh kosong');
                return;
            }

            if ($this->customProductPrice <= 0) {
                session()->flash('error', 'Harga harus lebih dari 0');
                return;
            }

            if ($this->customProductQuantity < 1) {
                session()->flash('error', 'Kuantitas minimal 1');
                return;
            }

            $sessionId = session()->getId();

            // Create cart item in database
            $cartItem = CartItem::create([
                'session_id' => $sessionId,
                'product_id' => null,
                'product_name' => trim($this->customProductName),
                'price' => $this->customProductPrice,
                'price_type' => 'custom',
                'quantity' => $this->customProductQuantity,
                'is_custom' => true,
            ]);

            Log::info('CartItem created', ['cart_item' => $cartItem->toArray()]);

            // Add to local cart
            $key = 'custom_' . $cartItem->id;
            $this->cart[$key] = [
                'id' => $cartItem->id,
                'product_id' => null,
                'name' => trim($this->customProductName),
                'price' => $this->customProductPrice,
                'price_type' => 'custom',
                'quantity' => $this->customProductQuantity,
                'is_custom' => true,
                'stock_available' => null,
            ];

            // Reset form
            $this->resetCustomProductForm();

            $this->calculateCartTotals();

            // Force update paid amount
            $this->updatePaidAmount();

            session()->flash('success', 'Produk kustom berhasil ditambahkan ke keranjang');

            Log::info('Custom product added successfully');
        } catch (\Exception $e) {
            Log::error('Error adding custom product: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetCustomProductForm()
    {
        $this->customProductName = '';
        $this->customProductPrice = 0;
        $this->customProductQuantity = 1;
    }

    public function decrementCustomQuantity()
    {
        if ($this->customProductQuantity > 1) {
            $this->customProductQuantity--;
        }
        // Force update
        $this->dispatch('quantity-updated');
    }

    public function incrementCustomQuantity()
    {
        if ($this->customProductQuantity < 999) {
            $this->customProductQuantity++;
        }
        // Force update
        $this->dispatch('quantity-updated');
    }

    public function updatedDiscount()
    {
        $this->updatePaidAmount();
    }

    public function updatedTax()
    {
        $this->updatePaidAmount();
    }

    // Auto-update paid amount when cart changes
    public function updatedCart()
    {
        $this->updatePaidAmount();
    }

    public function updatePaidAmount()
    {
        // Auto-update paid amount to match total when discount/tax changes
        if (!empty($this->cart)) {
            $this->paidAmount = $this->getTotalProperty();
            // Force update to frontend
            $this->dispatch('paid-amount-updated', paidAmount: $this->paidAmount);
        }
    }

    public function addToCart($productId, $priceType = 'retail', $quantity = 1)
    {
        // Redirect to the more robust method
        $this->addToCartWithQuantity($productId, $priceType, $quantity);
    }

    public function addToCartWithQuantity($productId, $priceType = 'retail', $quantity = 1)
    {
        $product = Product::find($productId);

        if (!$product) {
            session()->flash('error', 'Produk tidak ditemukan');
            return;
        }

        if ($product->stock_quantity <= 0) {
            session()->flash('error', 'Stok produk habis');
            return;
        }

        if ($quantity <= 0) {
            session()->flash('error', 'Quantity harus lebih dari 0');
            return;
        }

        $price = $priceType === 'wholesale' ? $product->wholesale_price : $product->retail_price;
        $cartKey = $productId . '_' . $priceType;
        $sessionId = session()->getId();

        if (isset($this->cart[$cartKey])) {
            $newQuantity = $this->cart[$cartKey]['quantity'] + $quantity;
            if ($newQuantity > $product->stock_quantity) {
                session()->flash('error', 'Stok tidak mencukupi');
                return;
            }

            // Update database
            $cartItem = CartItem::find($this->cart[$cartKey]['id']);
            $cartItem->update(['quantity' => $newQuantity]);

            // Update local cart
            $this->cart[$cartKey]['quantity'] = $newQuantity;
        } else {
            if ($quantity > $product->stock_quantity) {
                session()->flash('error', 'Stok tidak mencukupi');
                return;
            }

            // Create in database
            $cartItem = CartItem::create([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'product_name' => $product->name,
                'price' => $price,
                'price_type' => $priceType,
                'quantity' => $quantity,
                'is_custom' => false,
            ]);

            // Add to local cart
            $this->cart[$cartKey] = [
                'id' => $cartItem->id,
                'product_id' => $productId,
                'name' => $product->name,
                'price' => $price,
                'price_type' => $priceType,
                'quantity' => $quantity,
                'is_custom' => false,
                'stock_available' => $product->stock_quantity,
            ];
        }

        $this->calculateCartTotals();
    }

    public function removeFromCart($cartKey)
    {
        if (isset($this->cart[$cartKey])) {
            // Remove from database
            CartItem::find($this->cart[$cartKey]['id'])->delete();

            // Remove from local cart
            unset($this->cart[$cartKey]);
        }
        $this->calculateCartTotals();
    }

    public function updateQuantity($cartKey, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($cartKey);
            return;
        }

        $item = $this->cart[$cartKey];

        // Check stock for non-custom products
        if (!$item['is_custom'] && $quantity > $item['stock_available']) {
            session()->flash('error', 'Stok tidak mencukupi');
            return;
        }

        // Update database
        $cartItem = CartItem::find($item['id']);
        $cartItem->update(['quantity' => $quantity]);

        // Update local cart
        $this->cart[$cartKey]['quantity'] = $quantity;
        $this->calculateCartTotals();
    }

    public function calculateCartTotals()
    {
        // Auto-update paid amount to match new total when cart changes
        if (!empty($this->cart)) {
            // Force update paid amount immediately
            $this->paidAmount = $this->getTotalProperty();
        }

        // Dispatch events to update frontend
        $this->dispatch('cart-updated');
        $this->dispatch('paid-amount-updated', paidAmount: $this->paidAmount);
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getTotalProperty()
    {
        return $this->subtotal - $this->discount + $this->tax;
    }

    public function getChangeProperty()
    {
        return max(0, $this->paidAmount - $this->getTotalProperty());
    }

    public function setPaidAmountToTotal()
    {
        $this->paidAmount = $this->getTotalProperty();
        $this->dispatch('paid-amount-updated', paidAmount: $this->paidAmount);
    }

    public function setPaidAmount($amount)
    {
        $this->paidAmount = $amount;
    }

    public function processTransaction()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong');
            return;
        }

        if ($this->paidAmount < $this->total) {
            session()->flash('error', 'Pembayaran tidak mencukupi');
            return;
        }

        try {
            DB::beginTransaction();

            // Create transaction
            $transaction = Transaction::create([
                'customer_name' => $this->customerName ?: 'Walk-in Customer',
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax,
                'discount_amount' => $this->discount,
                'total_amount' => $this->total,
                'paid_amount' => $this->paidAmount,
                'change_amount' => $this->change,
                'payment_method' => $this->paymentMethod,
                'status' => 'completed',
            ]);

            // Create transaction items and update stock
            foreach ($this->cart as $item) {
                // Create transaction item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'price_type' => $item['price_type'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Update product stock only for non-custom products
                if (!$item['is_custom'] && $item['product_id']) {
                    $product = Product::find($item['product_id']);

                    if ($product) {
                        $newStock = $product->stock_quantity - $item['quantity'];
                        $product->update(['stock_quantity' => $newStock]);

                        // Create stock movement record
                        StockMovement::create([
                            'product_id' => $item['product_id'],
                            'type' => 'out',
                            'quantity' => $item['quantity'],
                            'stock_before' => $product->stock_quantity,
                            'stock_after' => $newStock,
                            'reference_type' => 'transaction',
                            'reference_id' => $transaction->id,
                            'notes' => 'Penjualan - ' . $transaction->invoice_number,
                        ]);
                    }
                }
            }

            DB::commit();

            // Clear cart from database and local
            $sessionId = session()->getId();
            CartItem::clearCartBySession($sessionId);

            $this->cart = [];
            $this->customerName = '';
            $this->customProductName = '';
            $this->customProductPrice = '';
            $this->paidAmount = 0;
            $this->discount = 0;

            session()->flash('success', 'Transaksi berhasil! Invoice: ' . $transaction->invoice_number);

            // Store transaction ID in session for printing
            session(['transaction_id' => $transaction->id]);
            $this->resetCustomProductForm();
            // Dispatch event for Bluetooth printing
            $this->dispatch('transactionCompleted', $transaction->id);
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        // Clear cart from database
        $sessionId = session()->getId();
        CartItem::clearCartBySession($sessionId);

        // Clear local cart
        $this->cart = [];
        $this->customerName = '';
        $this->paidAmount = 0;
        $this->discount = 0;

        // Dispatch update to frontend
        $this->dispatch('paid-amount-updated', paidAmount: 0);
    }

    public function clearTransactionSession()
    {
        // Clear transaction ID from session after printing
        session()->forget('transaction_id');
    }

    public function render()
    {
        $query = Product::with('category')->active();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('barcode', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        $products = $query->paginate(12);
        $categories = Category::active()->get();

        return view('livewire.p-o-s.cashier', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
