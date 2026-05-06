<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'total_quantity' => $this->total_quantity,
            'total_price'    => $this->total_price,
            'items'          => CartItemResource::collection($this->items),
        ];
    }
}