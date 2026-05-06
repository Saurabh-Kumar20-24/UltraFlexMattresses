<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
             [
                'name'        => 'Mattresses',
                'slug'        => 'mattresses',
                'description' => 'Premium quality mattresses for every sleeping need.',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
             [
                'name'        => 'Pillows',
                'slug'        => 'pillows',
                'description' => 'Ergonomically designed pillows for perfect neck support.',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
             [
                'name'        => 'Kids Mattresses',
                'slug'        => 'kids-mattresses',
                'description' => 'Safe and comfortable mattresses designed for children.',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
            [
                'name'        => 'Orthopedic Range',
                'slug'        => 'orthopedic-range',
                'description' => 'Doctor recommended mattresses for back and joint support.',
                'sort_order'  => 4,
                'is_active'   => true,
            ],
             [
                'name'        => 'Travel Products',
                'slug'        => 'travel-products',
                'description' => 'Compact and lightweight sleep solutions for travel.',
                'sort_order'  => 5,
                'is_active'   => true,
            ],
            [
                'name'        => 'Bed Accessories',
                'slug'        => 'bed-accessories',
                'description' => 'Mattress protectors, toppers and other accessories.',
                'sort_order'  => 6,
                'is_active'   => true,
            ],
        ];

        foreach($categories as $category){
            Category::create($category);
        }
    }
}
