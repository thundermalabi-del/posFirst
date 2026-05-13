<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionItem;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Reports extends Component
{
    use WithPagination;

    public $activeTab = 'sales';
    public $dateFrom = '';
    public $dateTo = '';
    public $selectedPeriod = 'today';

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

    public function render()
    {
        $data = [];

        if ($this->activeTab === 'sales') {
            $data = $this->salesData;
        }

        return view('livewire.reports', [
            'salesData' => $this->activeTab === 'sales' ? $data : null,
            'topProducts' => $this->activeTab === 'products' ? $this->topProducts : collect(),
            'lowStockProducts' => $this->activeTab === 'stock' ? $this->lowStockProducts : collect(),
        ]);
    }
}
