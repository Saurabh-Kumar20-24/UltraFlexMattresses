<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'cover_image_alt',
        'category',
        'tags',
        'meta_title',
        'meta_description',
        'is_published',
        'published_at',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'tags'         => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'views'        => 'integer',
        ];
    }

    // Auto-generate slug from title on creation
    protected static function booted(): void
    {
        static::creating(function ($blog) {
            $blog->slug = Str::slug($blog->title);
        });
    }


    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    // public function scopePublished($query)
    // {
    //     return $query->where('is_published', true)
    //                  ->where('published_at', '<=', now());
    // }

    // public function scopeDraft($query)
    // {
    //     return $query->where('is_published', false);
    // }

    // public function scopeByCategory($query, string $category)
    // {
    //     return $query->where('category', $category);
    // }

    // Increment views without touching updated_at
    public function incrementViews(): void
    {
        $this->timestamps = false;
        $this->increment('views');
        $this->timestamps = true;
    }

    // Auto-publish scheduled posts
    // Run this in a daily scheduled job
    public static function autoPublish(): void
    {
        static::draft()
              ->whereNotNull('published_at')
              ->where('published_at', '<=', now())
              ->update(['is_published' => true]);
    }

    // Get reading time in minutes
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->body));
        return (int) ceil($wordCount / 200); 
    }
}