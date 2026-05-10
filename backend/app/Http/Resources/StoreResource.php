<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'type'            => $this->type,
            'phone'           => $this->phone,
            'whatsapp'        => $this->whatsapp,
            'whatsapp_url'    => $this->whatsapp_url,
            'email'           => $this->email,
            'address'         => $this->address,
            'landmark'        => $this->landmark,
            'city'            => $this->city,
            'state'           => $this->state,
            'pincode'         => $this->pincode,
            'latitude'        => $this->latitude,
            'longitude'       => $this->longitude,
            'google_maps_url' => $this->google_maps_url,
            'business_hours'  => $this->business_hours,
            'is_open_now'     => $this->isOpenNow(),
            'store_image'     => $this->store_image
                                      ? asset('storage/' . $this->store_image)
                                      : null,
            'sort_order'      => $this->sort_order,
        ];
    }
}