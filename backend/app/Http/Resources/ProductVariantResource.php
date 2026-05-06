<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'size'      => $this->size,
            'price'     => $this->price,
            'stock_qty' => $this->stock,   
            'sku'       => $this->sku,
            'in_stock'  => $this->stock > 0, 
            'is_active' => $this->is_active,
        ];
    }
}