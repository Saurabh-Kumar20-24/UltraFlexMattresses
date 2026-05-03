<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'phone',
        'whatsapp',
        'email',
        'address',
        'landmark',
        'city',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'google_maps_url',
        'business_hours',
        'store_image',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'business_hours' => 'array',
            'latitude'       => 'decimal:7',
            'longitude'      => 'decimal:7',
            'is_active'      => 'boolean',
            'sort_order'     => 'integer',
        ];
    }

    // Scopes
    // public function scopeActive($query)
    // {
    //     return $query->where('is_active', true);
    // }

    // public function scopeOrdered($query)
    // {
    //     return $query->orderBy('sort_order');
    // }

    // public function scopeByCity($query, string $city)
    // {
    //     return $query->where('city', $city);
    // }

    // public function scopeByState($query, string $state)
    // {
    //     return $query->where('state', $state);
    // }

    // public function scopeCompanyOwned($query)
    // {
    //     return $query->where('type', 'company_owned');
    // }

    // Check if store is open right now
    public function isOpenNow(): bool
    {
        if (!$this->business_hours) return false;

        $day   = strtolower(now()->format('l'));
        $hours = $this->business_hours[$day] ?? null;

        if (!$hours || !$hours['open'] || !$hours['close']) return false;

        $now   = now()->format('H:i');
        return $now >= $hours['open'] && $now <= $hours['close'];
    }

    
    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->whatsapp) return null;
        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);
        return "https://wa.me/91{$number}";
    }

    
    public function distanceFrom(float $lat, float $lng): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($this->latitude - $lat);
        $dLng = deg2rad($this->longitude - $lng);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat)) * cos(deg2rad($this->latitude)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}