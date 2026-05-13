<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'product_id',
        'product_name',
        'price',
        'price_type',
        'quantity',
        'is_custom'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'is_custom' => 'boolean'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    public static function getCartBySession(string $sessionId)
    {
        return self::where('session_id', $sessionId)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function clearCartBySession(string $sessionId)
    {
        return self::where('session_id', $sessionId)->delete();
    }
}
