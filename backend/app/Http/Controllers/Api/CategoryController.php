<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index(): JsonResponse
    {
        $categories = Category::active()
                               ->ordered()
                               ->get();

        return response()->json([
            'success' => true,
            'data'    => CategoryResource::collection($categories),
        ]);
    }

    // GET /api/categories/{slug}
    public function show(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
                            ->where('is_active', true)
                            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new CategoryResource($category),
        ]);
    }
}