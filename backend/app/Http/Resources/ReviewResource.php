<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'rating'               => $this->rating,
            'title'                => $this->title,
            'comment'              => $this->comment,
            'images'               => collect($this->images ?? [])
                                          ->map(fn($img) => asset('storage/' . $img))
                                          ->toArray(),
            'is_verified_purchase' => $this->is_verified_purchase,
            'is_approved'          => $this->is_approved,
            'helpful_count'        => $this->helpful_count,
            'admin_reply'          => $this->admin_reply,
            'replied_at'           => $this->replied_at?->toDateString(),
            'user'                 => [
                'name'   => $this->user->name,
                'avatar' => $this->user->avatar
                                ? asset('storage/' . $this->user->avatar)
                                : null,
            ],
            'created_at'           => $this->created_at->toDateString(),
        ];
    }
}