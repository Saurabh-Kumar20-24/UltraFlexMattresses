<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'image_path' => 'products/placeholder.jpg',
            'alt_text'   => 'UltraFlex Mattress',
            'sort_order' => fake()->numberBetween(1, 5),
            'is_primary' => false,
        ];
    }

  
    public function primary(): static
    {
        return $this->state(fn () => [
            'is_primary' => true,
            'sort_order' => 0,
        ]);
    }
}