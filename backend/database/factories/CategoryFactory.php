<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $name=fake()->unique()->randomElement([
               'Mattresses',
                'Pillows',
                'Bed Accessories',
                'Travel Products',
                'Kids Mattresses',
                'Orthopedic Range'
            ]);
        return [
           'name'=> $name,
           'slug'=> Str::slug($name),
           'description' => fake()->sentences(12),
           'image'=> null,
           'sort_order'=> fake()->numberBetween(1,10),
           'is_active' => true,
        ];
    }
}
