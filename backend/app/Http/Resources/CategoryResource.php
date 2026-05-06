<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'image'       => $this->image
                                ? asset('storage/' . $this->image)
                                : null,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
        ];
    }
}