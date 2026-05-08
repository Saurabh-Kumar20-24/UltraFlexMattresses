<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'product_name'  => $this->product_name,
            'variant_size'  => $this->variant_size,
            'product_sku'   => $this->product_sku,
            'product_image' => $this->product_image
                                    ? asset('storage/' . $this->product_image)
                                    : null,
            'quantity'      => $this->quantity,
            'unit_price'    => $this->unit_price,
            'subtotal'      => $this->subtotal,
        ];
    }
}