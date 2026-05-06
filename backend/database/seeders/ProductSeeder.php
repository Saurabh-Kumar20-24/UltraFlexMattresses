<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        
        $products = [
            [
                'name'           => 'Xpert Grid',
                'category'       => 'Mattresses',
                'description'    => 'The Xpert Grid mattress features advanced grid technology that provides superior pressure relief and airflow. Designed for those who want the perfect balance of support and comfort.',
                'thickness'      => '150mm',
                'warranty_years' => 10,
                'is_featured'    => true,
                'variants'       => [
                    ['size' => 'Single (72"x36")',       'price' => 15999, 'sku' => 'UF-XG-7236'],
                    ['size' => 'Single Large (78"x36")', 'price' => 17999, 'sku' => 'UF-XG-7836'],
                    ['size' => 'Double (72"x48")',        'price' => 22999, 'sku' => 'UF-XG-7248'],
                    ['size' => 'Queen (78"x60")',         'price' => 34999, 'sku' => 'UF-XG-7860'],
                    ['size' => 'King (78"x72")',          'price' => 40397, 'sku' => 'UF-XG-7872'],
                ],
            ],
            [
                'name'           => 'Essence Plus',
                'category'       => 'Mattresses',
                'description'    => 'The Essence Plus is crafted with premium memory foam layers that adapt perfectly to your body shape. Ideal for all sleeping positions with long-lasting comfort.',
                'thickness'      => '125mm',
                'warranty_years' => 25,
                'is_featured'    => true,
                'variants'       => [
                    ['size' => 'Single (72"x36")',       'price' => 9562,  'sku' => 'UF-EP-7236'],
                    ['size' => 'Single Large (78"x36")', 'price' => 10999, 'sku' => 'UF-EP-7836'],
                    ['size' => 'Double (72"x48")',        'price' => 14999, 'sku' => 'UF-EP-7248'],
                    ['size' => 'Queen (78"x60")',         'price' => 19999, 'sku' => 'UF-EP-7860'],
                    ['size' => 'King (78"x72")',          'price' => 24860, 'sku' => 'UF-EP-7872'],
                ],
            ],
            [
                'name'           => 'Ortho Comfort',
                'category'       => 'Orthopedic Range',
                'description'    => 'Designed in consultation with orthopedic specialists, the Ortho Comfort mattress provides targeted support for your spine and joints for a pain-free sleep experience.',
                'thickness'      => '175mm',
                'warranty_years' => 15,
                'is_featured'    => true,
                'variants'       => [
                    ['size' => 'Single (72"x36")',       'price' => 18999, 'sku' => 'UF-OC-7236'],
                    ['size' => 'Single Large (78"x36")', 'price' => 20999, 'sku' => 'UF-OC-7836'],
                    ['size' => 'Double (72"x48")',        'price' => 26999, 'sku' => 'UF-OC-7248'],
                    ['size' => 'Queen (78"x60")',         'price' => 36999, 'sku' => 'UF-OC-7860'],
                    ['size' => 'King (78"x72")',          'price' => 44999, 'sku' => 'UF-OC-7872'],
                ],
            ],
            [
                'name'           => 'Car L2 Memory Pillow',
                'category'       => 'Travel Products',
                'description'    => 'A compact memory foam pillow designed for travel comfort. Fits perfectly in any car headrest and provides excellent neck support on long journeys.',
                'thickness'      => null,
                'warranty_years' => 1,
                'is_featured'    => true,
                'variants'       => [
                    ['size' => 'Standard', 'price' => 1199, 'sku' => 'UF-CL2-STD'],
                ],
            ],
            [
                'name'           => 'Neck Rest Pillow',
                'category'       => 'Pillows',
                'description'    => 'Ergonomically contoured neck rest pillow that provides perfect cervical support. Ideal for office use, travel, and home relaxation.',
                'thickness'      => null,
                'warranty_years' => 1,
                'is_featured'    => false,
                'variants'       => [
                    ['size' => 'Standard', 'price' => 699,  'sku' => 'UF-NR-STD'],
                    ['size' => 'Large',    'price' => 899,  'sku' => 'UF-NR-LRG'],
                ],
            ],
            [
                'name'           => 'Cloud Nine',
                'category'       => 'Mattresses',
                'description'    => 'Experience cloud-like comfort with the Cloud Nine mattress. Its ultra-soft top layer combined with high-density base foam delivers unmatched softness without compromising support.',
                'thickness'      => '200mm',
                'warranty_years' => 10,
                'is_featured'    => false,
                'variants'       => [
                    ['size' => 'Single (72"x36")',       'price' => 21999, 'sku' => 'UF-CN-7236'],
                    ['size' => 'Single Large (78"x36")', 'price' => 23999, 'sku' => 'UF-CN-7836'],
                    ['size' => 'Double (72"x48")',        'price' => 29999, 'sku' => 'UF-CN-7248'],
                    ['size' => 'Queen (78"x60")',         'price' => 42999, 'sku' => 'UF-CN-7860'],
                    ['size' => 'King (78"x72")',          'price' => 52999, 'sku' => 'UF-CN-7872'],
                ],
            ],
            [
                'name'           => 'Dream Lite',
                'category'       => 'Kids Mattresses',
                'description'    => 'Specially designed for children, the Dream Lite mattress offers the right firmness for growing bodies. Made with child-safe materials and a washable cover.',
                'thickness'      => '100mm',
                'warranty_years' => 5,
                'is_featured'    => false,
                'variants'       => [
                    ['size' => 'Single (72"x36")',       'price' => 7999,  'sku' => 'UF-DL-7236'],
                    ['size' => 'Single Large (78"x36")', 'price' => 8999,  'sku' => 'UF-DL-7836'],
                ],
            ],
        ];

        foreach ($products as $productData) {

            $category = Category::where('name', $productData['category'])->first();

            $product = Product::create([
                'category_id'    => $category->id,
                'name'           => $productData['name'],
                'slug'           => Str::slug($productData['name']),
                'description'    => $productData['description'],
                'thickness'      => $productData['thickness'],
                'warranty_years' => $productData['warranty_years'],
                'is_featured'    => $productData['is_featured'],
                'is_active'      => true,
            ]);

            foreach ($productData['variants'] as $index => $variantData) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size'       => $variantData['size'],
                    'price'      => $variantData['price'],
                    'stock'  => rand(10, 50),
                    'sku'        => $variantData['sku'],
                    'is_active'  => true,
                ]);
            }

            // Create a placeholder primary image for each product
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => 'products/placeholder.jpg',
                'alt_text'   => $productData['name'] . ' - UltraFlex Mattress',
                'sort_order' => 0,
                'is_primary' => true,
            ]);
        }
    }
}