<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Blog::with('author')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $request->status === 'published'
                ? $query->where('is_published', true)
                : $query->where('is_published', false);
        }

        $blogs = $query->paginate(15);

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

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'            => 'required|string|max:200|unique:blogs,title',
            'excerpt'          => 'nullable|string',
            'body'             => 'required|string',
            'category'         => 'nullable|string|max:100',
            'tags'             => 'nullable|array',
            'meta_title'       => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320',
            'is_published'     => 'boolean',
            'published_at'     => 'nullable|date',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')
                                      ->store('blogs', 'public');
        }

        $blog = Blog::create([
            'user_id'          => $request->user()->id,
            'title'            => $request->title,
            'slug'             => Str::slug($request->title),
            'excerpt'          => $request->excerpt,
            'body'             => $request->body,
            'cover_image'      => $coverImagePath,
            'category'         => $request->category,
            'tags'             => $request->tags ?? [],
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_published'     => $request->boolean('is_published'),
            'published_at'     => $request->is_published
                                    ? ($request->published_at ?? now())
                                    : $request->published_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully.',
            'data'    => new BlogResource($blog->load('author')),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title'            => 'required|string|max:200|unique:blogs,title,' . $id,
            'excerpt'          => 'nullable|string',
            'body'             => 'required|string',
            'category'         => 'nullable|string|max:100',
            'tags'             => 'nullable|array',
            'meta_title'       => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320',
            'is_published'     => 'boolean',
            'published_at'     => 'nullable|date',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $data = [
            'title'            => $request->title,
            'excerpt'          => $request->excerpt,
            'body'             => $request->body,
            'category'         => $request->category,
            'tags'             => $request->tags ?? [],
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_published'     => $request->boolean('is_published'),
            'published_at'     => $request->published_at,
        ];

        if ($request->hasFile('cover_image')) {
            if ($blog->cover_image) {
                Storage::disk('public')->delete($blog->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')
                                           ->store('blogs', 'public');
        }

        $blog->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully.',
            'data'    => new BlogResource($blog->fresh()->load('author')),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);

        if ($blog->cover_image) {
            Storage::disk('public')->delete($blog->cover_image);
        }

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully.',
        ]);
    }
}