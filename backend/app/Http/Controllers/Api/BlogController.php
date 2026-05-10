<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
   
    public function index(Request $request): JsonResponse
    {
        $query = Blog::with('author')
                     ->published();

  
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $query->latest('published_at');

        $perPage = $request->per_page ?? 9;
        $blogs   = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => BlogResource::collection($blogs),
            'meta'    => [
                'current_page' => $blogs->currentPage(),
                'last_page'    => $blogs->lastPage(),
                'total'        => $blogs->total(),
            ],
        ]);
    }


    public function show(string $slug): JsonResponse
    {
        $blog = Blog::with('author')
                    ->published()
                    ->where('slug', $slug)
                    ->firstOrFail();

        $blog->incrementViews();

        return response()->json([
            'success' => true,
            'data'    => new BlogResource($blog),
        ]);
    }

    public function recent(): JsonResponse
    {
        $blogs = Blog::with('author')
                     ->published()
                     ->latest('published_at')
                     ->take(3)
                     ->get();

        return response()->json([
            'success' => true,
            'data'    => BlogResource::collection($blogs),
        ]);
    }


    public function categories(): JsonResponse
    {
        $categories = Blog::published()
                          ->whereNotNull('category')
                          ->distinct()
                          ->pluck('category');

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }
}