<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    // Save preferences
    // POST /api/preferences
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shopping_for'  => 'required|in:myself,partner,child,parents',
            'sleep_concern' => 'required|in:back_pain,sleep_hot,partner_disturbance,comfort',
            'budget_range'  => 'required|in:under_10k,10k_25k,25k_50k,no_limit',
            'session_id'    => 'nullable|string',
        ]);

        $user = $request->user(); // null if guest

        // Find existing preference or create new
        $preference = UserPreference::updateOrCreate(
            [
                'user_id'    => $user?->id,
                'session_id' => $user ? null : $request->session_id,
            ],
            [
                'shopping_for'    => $request->shopping_for,
                'sleep_concern'   => $request->sleep_concern,
                'budget_range'    => $request->budget_range,
                'modal_completed' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Preferences saved.',
            'data'    => $preference,
        ]);
    }

    // Get personalized products based on preferences
    // GET /api/preferences/recommendations
    public function recommendations(Request $request): JsonResponse
    {
        $user      = $request->user();
        $sessionId = $request->header('X-Session-ID');

        // Find preference
        $preference = null;

        if ($user) {
            $preference = UserPreference::where('user_id', $user->id)->first();
        }

        if (!$preference && $sessionId) {
            $preference = UserPreference::where('session_id', $sessionId)->first();
        }

        // No preference found — return featured products
        if (!$preference || !$preference->modal_completed) {
            $products = \App\Models\Product::with(['category', 'variants', 'images'])
                                           ->where('is_active', true)
                                           ->where('is_featured', true)
                                           ->take(8)
                                           ->get();

            return response()->json([
                'success'      => true,
                'personalized' => false,
                'data'         => \App\Http\Resources\ProductResource::collection($products),
            ]);
        }

        // Build personalized query
        $query = \App\Models\Product::with(['category', 'variants', 'images'])
                                    ->where('is_active', true);

        // Filter by sleep concern → category
        if ($preference->sleep_concern === 'back_pain') {
            $query->whereHas('category', fn($q) =>
                $q->whereIn('slug', ['orthopedic-range', 'mattresses'])
            );
        }

        if ($preference->sleep_concern === 'sleep_hot') {
            $query->where('description', 'like', '%cool%')
                  ->orWhere('name', 'like', '%cool%');
        }

        if ($preference->shopping_for === 'child') {
            $query->whereHas('category', fn($q) =>
                $q->where('slug', 'kids-mattresses')
            );
        }

        // Filter by budget → variant price range
        $budgetMap = [
            'under_10k' => [0,     10000],
            '10k_25k'   => [10000, 25000],
            '25k_50k'   => [25000, 50000],
            'no_limit'  => [0,     999999],
        ];

        $range = $budgetMap[$preference->budget_range] ?? [0, 999999];

        $query->whereHas('variants', fn($q) =>
            $q->whereBetween('price', $range)
        );

        $products = $query->take(8)->get();

        // If no products match — fallback to featured
        if ($products->isEmpty()) {
            $products = \App\Models\Product::with(['category', 'variants', 'images'])
                                           ->where('is_active', true)
                                           ->where('is_featured', true)
                                           ->take(8)
                                           ->get();
        }

        return response()->json([
            'success'      => true,
            'personalized' => true,
            'preference'   => [
                'shopping_for'  => $preference->shopping_for,
                'sleep_concern' => $preference->sleep_concern,
                'budget_range'  => $preference->budget_range,
            ],
            'data' => \App\Http\Resources\ProductResource::collection($products),
        ]);
    }
}