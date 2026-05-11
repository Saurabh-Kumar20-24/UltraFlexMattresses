<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function index(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
                          ->where('is_active', true)
                          ->firstOrFail();

        $reviews = Review::where('product_id', $product->id)
                         ->where('is_approved', true)
                         ->with('user')
                         ->latest()
                         ->paginate(10);

        $breakdown = Review::where('product_id', $product->id)
                           ->where('is_approved', true)
                           ->selectRaw('rating, count(*) as count')
                           ->groupBy('rating')
                           ->pluck('count', 'rating');

        return response()->json([
            'success' => true,
            'data'    => ReviewResource::collection($reviews),
            'meta'    => [
                'current_page'     => $reviews->currentPage(),
                'last_page'        => $reviews->lastPage(),
                'total'            => $reviews->total(),
                'average_rating'   => round(
                                          Review::where('product_id', $product->id)
                                                ->where('is_approved', true)
                                                ->avg('rating') ?? 0,
                                          1
                                      ),
                'rating_breakdown' => [
                    '5' => $breakdown[5] ?? 0,
                    '4' => $breakdown[4] ?? 0,
                    '3' => $breakdown[3] ?? 0,
                    '2' => $breakdown[2] ?? 0,
                    '1' => $breakdown[1] ?? 0,
                ],
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'title'      => 'nullable|string|max:150',
            'comment'    => 'nullable|string|max:1000',
            'images'     => 'nullable|array|max:3',
            'images.*'   => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user    = $request->user();
        $product = Product::findOrFail($request->product_id);

        $existing = Review::where('user_id', $user->id)
                          ->where('product_id', $product->id)
                          ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product.',
            ], 422);
        }

        $isVerified = Order::where('user_id', $user->id)
                           ->where('status', 'delivered')
                           ->whereHas('items', fn($q) =>
                               $q->where('product_id', $product->id)
                           )
                           ->exists();

        $orderId = null;
        if ($isVerified) {
            $orderId = Order::where('user_id', $user->id)
                            ->where('status', 'delivered')
                            ->whereHas('items', fn($q) =>
                                $q->where('product_id', $product->id)
                            )
                            ->value('id');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        $review = Review::create([
            'user_id'              => $user->id,
            'product_id'           => $product->id,
            'order_id'             => $orderId,
            'rating'               => $request->rating,
            'title'                => $request->title,
            'comment'              => $request->comment,
            'images'               => $imagePaths,
            'is_verified_purchase' => $isVerified,
            'is_approved'          => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully. It will appear after approval.',
            'data'    => new ReviewResource($review),
        ], 201);
    }


    public function helpful(int $id): JsonResponse
    {
        $review = Review::where('is_approved', true)->findOrFail($id);
        $review->incrementHelpful();

        return response()->json([
            'success'       => true,
            'helpful_count' => $review->fresh()->helpful_count,
        ]);
    }
}