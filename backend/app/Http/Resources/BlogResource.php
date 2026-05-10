<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'excerpt'          => $this->excerpt,
            'body'             => $this->when(
                                      $request->routeIs('blogs.show'),
                                      $this->body
                                  ),
            'cover_image'      => $this->cover_image
                                       ? asset('storage/' . $this->cover_image)
                                       : null,
            'cover_image_alt'  => $this->cover_image_alt,
            'category'         => $this->category,
            'tags'             => $this->tags ?? [],
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'reading_time'     => $this->reading_time,
            'views'            => $this->views,
            'published_at'     => $this->published_at?->toDateString(),
            'author'           => [
                'name'   => $this->author->name,
                'avatar' => $this->author->avatar
                                ? asset('storage/' . $this->author->avatar)
                                : null,
            ],
        ];
    }
}