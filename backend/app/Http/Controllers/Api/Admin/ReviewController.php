<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['user', 'product'])->latest();

        if ($request->filled('status')) {
            $request->status === 'approved'
                ? $query->where('is_approved', true)
                : $query->where('is_approved', false);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )->orWhereHas('product', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            );
        }

        $reviews = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => ReviewResource::collection($reviews),
            'meta'    => [
                'current_page' => $reviews->currentPage(),
                'last_page'    => $reviews->lastPage(),
                'total'        => $reviews->total(),
            ],
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        $request->validate([
            'action'           => 'required|in:approve,reject,reply',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
            'admin_reply'      => 'required_if:action,reply|nullable|string|max:1000',
        ]);

        switch ($request->action) {

            case 'approve':
                $review->approve($request->user()->id);
                $message = 'Review approved successfully.';
                break;

            case 'reject':
                $review->reject(
                    $request->rejection_reason,
                    $request->user()->id
                );
                $message = 'Review rejected successfully.';
                break;

            case 'reply':
                $review->addReply($request->admin_reply);
                $message = 'Reply added successfully.';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => new ReviewResource($review->fresh()->load('user', 'product')),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ]);
    }
}