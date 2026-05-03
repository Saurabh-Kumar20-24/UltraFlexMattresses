<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'payment_id',
        'razorpay_order_id',
        'subtotal',
        'discount_amount',
        'shipping_charge',
        'total_amount',
        'shipping_address',
        'coupon_code',
        'customer_notes',
        'admin_notes',
        'tracking_number',
        'shipping_provider',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',  
            'subtotal'         => 'decimal:2',
            'discount_amount'  => 'decimal:2',
            'shipping_charge'  => 'decimal:2',
            'total_amount'     => 'decimal:2',
            'shipped_at'       => 'datetime',
            'delivered_at'     => 'datetime',
            'cancelled_at'     => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($order) {
            $year  = now()->format('Y');
            $count = Order::whereYear('created_at', $year)->count() + 1;
            $order->order_number = 'UF-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        });
    }

  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}