<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'variants', 'images'])
                        ->withTrashed(); // include soft deleted

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $request->status === 'active'
                ? $query->where('is_active', true)
                : $query->where('is_active', false);
        }

        $products = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

  
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'           => 'required|string|max:150|unique:products,name',
            'category_id'    => 'required|integer|exists:categories,id',
            'description'    => 'required|string',
            'thickness'      => 'nullable|string|max:50',
            'warranty_years' => 'nullable|integer|min:1|max:25',
            'is_featured'    => 'boolean',
            'is_active'      => 'boolean',
            'variants'       => 'required|array|min:1',
            'variants.*.size'      => 'required|string|max:50',
            'variants.*.price'     => 'required|numeric|min:0',
            'variants.*.stock_qty' => 'required|integer|min:0',
            'variants.*.sku'       => 'required|string|max:100',
        ]);

        $product = DB::transaction(function () use ($request) {
            $product = Product::create([
                'category_id'    => $request->category_id,
                'name'           => $request->name,
                'slug'           => Str::slug($request->name),
                'description'    => $request->description,
                'thickness'      => $request->thickness,
                'warranty_years' => $request->warranty_years,
                'is_featured'    => $request->boolean('is_featured'),
                'is_active'      => $request->boolean('is_active', true),
            ]);

            foreach ($request->variants as $variantData) {
                $product->variants()->create($variantData);
            }

            return $product;
        });

        $product->load(['category', 'variants', 'images']);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data'    => new ProductResource($product),
        ], 201);
    }

    // PUT /api/admin/products/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:150|unique:products,name,' . $id,
            'category_id'    => 'required|integer|exists:categories,id',
            'description'    => 'required|string',
            'thickness'      => 'nullable|string|max:50',
            'warranty_years' => 'nullable|integer|min:1|max:25',
            'is_featured'    => 'boolean',
            'is_active'      => 'boolean',
        ]);

        $product->update([
            'category_id'    => $request->category_id,
            'name'           => $request->name,
            'description'    => $request->description,
            'thickness'      => $request->thickness,
            'warranty_years' => $request->warranty_years,
            'is_featured'    => $request->boolean('is_featured'),
            'is_active'      => $request->boolean('is_active'),
        ]);

        $product->load(['category', 'variants', 'images']);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data'    => new ProductResource($product),
        ]);
    }

    // DELETE /api/admin/products/{id}
    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete(); // soft delete

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}