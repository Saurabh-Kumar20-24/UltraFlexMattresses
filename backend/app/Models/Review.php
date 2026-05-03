<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'images',
        'is_verified_purchase',
        'is_approved',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'admin_reply',
        'replied_at',
        'helpful_count',
        'unhelpful_count',
    ];

    protected function casts(): array
    {
        return [
            'images'               => 'array',
            'is_verified_purchase' => 'boolean',
            'is_approved'          => 'boolean',
            'approved_at'          => 'datetime',
            'replied_at'           => 'datetime',
            'rating'               => 'integer',
            'helpful_count'        => 'integer',
            'unhelpful_count'      => 'integer',
        ];
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    // public function scopeApproved($query)
    // {
    //     return $query->where('is_approved', true);
    // }

    // public function scopePending($query)
    // {
    //     return $query->where('is_approved', false);
    // }

    // public function scopeVerified($query)
    // {
    //     return $query->where('is_verified_purchase', true);
    // }

    // public function scopeByRating($query, int $rating)
    // {
    //     return $query->where('rating', $rating);
    // }

    // Increment helpful without touching updated_at
    public function incrementHelpful(): void
    {
        $this->timestamps = false;
        $this->increment('helpful_count');
        $this->timestamps = true;
    }

    
    public function approve(int $adminId): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
        ]);
    }

    // Reject the review
    public function reject(string $reason, int $adminId): void
    {
        $this->update([
            'is_approved'      => false,
            'rejection_reason' => $reason,
            'approved_by'      => $adminId,
        ]);
    }

    // Add admin reply
    public function addReply(string $reply): void
    {
        $this->update([
            'admin_reply' => $reply,
            'replied_at'  => now(),
        ]);
    }
}