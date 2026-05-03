<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warranty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'warranty_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_city',
        'customer_state',
        'customer_pincode',
        'product_name',
        'product_sku',
        'variant_size',
        'purchase_date',
        'purchase_from',
        'purchase_amount',
        'expiry_date',
        'warranty_years',
        'status',
        'claim_reason',
        'claimed_at',
        'admin_remarks',
        'invoice_image',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date'   => 'date',
            'expiry_date'     => 'date',
            'claimed_at'      => 'datetime',
            'purchase_amount' => 'decimal:2',
            'warranty_years'  => 'integer',
        ];
    }

    // Auto-generate warranty number on creation
    protected static function booted(): void
    {
        static::creating(function ($warranty) {
            $year  = now()->format('Y');
            $count = Warranty::whereYear('created_at', $year)->count() + 1;
            $warranty->warranty_number = 'UF-WR-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        });
    }

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    // public function scopeActive($query)
    // {
    //     return $query->where('status', 'active');
    // }

    // public function scopeExpired($query)
    // {
    //     return $query->where('status', 'expired');
    // }

    // public function scopeClaimed($query)
    // {
    //     return $query->where('status', 'claimed');
    // }

    // Check if warranty is still valid
    public function isValid(): bool
    {
        return $this->status === 'active' && $this->expiry_date->isFuture();
    }

    // Check if warranty has expired
    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    // Auto-expire warranties via scheduled command
    // Run this in a daily scheduled job
    public static function autoExpire(): void
    {
        static::active()
              ->where('expiry_date', '<', today())
              ->update(['status' => 'expired']);
    }
}