<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    // GET /api/products
    public function index(ProductFilterRequest $request): JsonResponse
    {
        $query = Product::with(['category', 'variants', 'images'])
            ->where('is_active', true);

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by price range
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }

        // Sorting
        switch ($request->sort_by) {
            case 'price_low':
                $query->withMin('variants', 'price')
                    ->orderBy('variants_min_price', 'asc');
                break;
            case 'price_high':
                $query->withMin('variants', 'price')
                    ->orderBy('variants_min_price', 'desc');
                break;
            case 'popular':
                $query->withCount('reviews')
                    ->orderBy('reviews_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage  = $request->per_page ?? 12;
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    // GET /api/products/{slug}
    public function show(string $slug): JsonResponse
    {
        $product = Product::with([
            'category',
            'variants'  => fn($q) => $q->where('is_active', true),
            'images'    => fn($q) => $q->orderBy('sort_order'),
            'reviews'   => fn($q) => $q->where('is_approved', true)
                ->with('user')
                ->latest()
                ->take(10),
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product),
        ]);
    }

    // GET /api/products/featured
    public function featured(): JsonResponse
    {
        $products = Product::with(['category', 'variants', 'images'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products),
        ]);
    }

    // GET /api/products/{slug}/related
    public function related(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $related = Product::with(['category', 'variants', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($related),
        ]);
    }
}
