<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'thickness'      => $this->thickness,
            'warranty_years' => $this->warranty_years,
            'is_featured'    => $this->is_featured,
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'variants'       => ProductVariantResource::collection($this->whenLoaded('variants')),
            'images'         => ProductImageResource::collection($this->whenLoaded('images')),
            'primary_image'  => new ProductImageResource(
                                    $this->whenLoaded('images',
                                        fn() => $this->images->where('is_primary', true)->first()
                                    )
                                ),
            'starting_price' => $this->whenLoaded('variants',
                                    fn() => $this->variants->min('price')
                                ),
            'average_rating' => $this->average_rating,
            'review_count'   => $this->review_count,
            'created_at'     => $this->created_at->toDateString(),
        ];
    }
}