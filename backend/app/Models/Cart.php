<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    // public function scopeForUser($query, int $userId)
    // {
    //     return $query->where('user_id', $userId);
    // }

    // public function scopeForSession($query, string $sessionId)
    // {
    //     return $query->where('session_id', $sessionId);
    // }

   
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }


    public function getTotalPriceAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->productVariant->price;
        });
    }


    public function isEmptyAttribute(): bool
    {
        return $this->items->isEmpty();
    }

    // Merge guest cart into user cart after login
    public function mergeWith(Cart $guestCart): void
    {
        foreach ($guestCart->items as $guestItem) {

            $existingItem = $this->items()
                                 ->where('product_variant_id', $guestItem->product_variant_id)
                                 ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $guestItem->quantity);
            } else {
                $guestItem->update(['cart_id' => $this->id]);
            }
        }

        $guestCart->delete();
    }
}