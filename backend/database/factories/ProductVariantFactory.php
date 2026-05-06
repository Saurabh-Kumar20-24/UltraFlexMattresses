<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        $sizes = [
            'Single'        => [72, 36],
            'Single Large'  => [78, 36],
            'Double'        => [72, 48],
            'Queen'         => [78, 60],
            'King'          => [78, 72],
        ];

        $sizeName   = fake()->randomElement(array_keys($sizes));
        $dimensions = $sizes[$sizeName];

        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'size'       => $sizeName . ' (' . $dimensions[0] . '"x' . $dimensions[1] . '")',
            'price'      => fake()->randomElement([
                             9999, 12999, 15999, 18999,
                             24999, 29999, 34999, 39999
                            ]),
            'stock'  => fake()->numberBetween(5, 50),
            'sku'        => 'UF-' . strtoupper(fake()->bothify('??-####')),
            'is_active'  => true,
        ];
    }
}