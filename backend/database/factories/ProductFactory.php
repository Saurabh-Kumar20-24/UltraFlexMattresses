<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $names = [
            'Xpert Grid', 'Essence Plus', 'Ortho Comfort',
            'Cool Sleep Pro', 'Dream Lite', 'Spine Care',
            'Cloud Nine', 'Posture Perfect', 'Euro Top Deluxe',
            'Nature Fresh',
        ];
         $name = fake()->unique()->randomElement($names);

        return [
            'category_id'    => Category::inRandomOrder()->first()->id,
            'name'           => $name,
            'slug'           => Str::slug($name),
            'description'    => fake()->paragraphs(3, true),
            'thickness'      => fake()->randomElement([
                                '100mm', '125mm', '150mm',
                                '175mm', '200mm'
                               ]),
            'warranty_years' => fake()->randomElement([5, 7, 10, 15, 25]),
            'is_featured'    => fake()->boolean(30), // 30% chance featured
            'is_active'      => true,
        ];

        
    }

    public function featured(): static
    {
        return $this->state(fn () => [
            'is_featured' => true,
        ]);
    }
}
