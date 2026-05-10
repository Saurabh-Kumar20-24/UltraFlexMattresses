<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'shopping_for',
        'sleep_concern',
        'budget_range',
        'modal_completed',
    ];

    protected function casts(): array
    {
        return [
            'modal_completed' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}