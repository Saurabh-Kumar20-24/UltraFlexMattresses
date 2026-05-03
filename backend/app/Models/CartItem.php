<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }


    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }


    public function getProductAttribute()
    {
        return $this->productVariant->product;
    }


    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->productVariant->price;
    }

    public function increaseQuantity(int $amount = 1): void
    {
        $this->increment('quantity', $amount);
    }

    // Decrease quantity — remove item if quantity reaches zero
    public function decreaseQuantity(int $amount = 1): void
    {
        if ($this->quantity <= $amount) {
            $this->delete();
        } else {
            $this->decrement('quantity', $amount);
        }
    }
}