<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'quantity'    => $this->quantity,
            'subtotal'    => $this->subtotal,
            'variant'     => [
                'id'       => $this->productVariant->id,
                'size'     => $this->productVariant->size,
                'price'    => $this->productVariant->price,
                'sku'      => $this->productVariant->sku,
                'in_stock' => $this->productVariant->stock_qty > 0,
            ],
            'product'     => [
                'id'            => $this->productVariant->product->id,
                'name'          => $this->productVariant->product->name,
                'slug'          => $this->productVariant->product->slug,
                'thumbnail'     => $this->productVariant->product->images
                                       ->where('is_primary', true)
                                       ->first()
                                       ? asset('storage/' . $this->productVariant->product->images->where('is_primary', true)->first()->image_path)
                                       : null,
            ],
        ];
    }
}