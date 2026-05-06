<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
 
    public function index(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);

        $cart->load([
            'items.productVariant.product.images',
        ]);

        return response()->json([
            'success' => true,
            'data'    => new CartResource($cart),
        ]);
    }


    public function store(AddToCartRequest $request): JsonResponse
    {
        $variant = ProductVariant::where('id', $request->product_variant_id)
                                 ->where('is_active', true)
                                 ->firstOrFail();

        if ($variant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.',
            ], 422);
        }

        $cart = $this->getOrCreateCart($request);

        $existingItem = $cart->items()
                             ->where('product_variant_id', $variant->id)
                             ->first();

        if ($existingItem) {

            $newQuantity = $existingItem->quantity + $request->quantity;

            if ($newQuantity > $variant->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available.',
                ], 422);
            }

            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity'           => $request->quantity,
            ]);
        }

        $cart->load(['items.productVariant.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart.',
            'data'    => new CartResource($cart),
        ], 201);
    }


    public function update(UpdateCartRequest $request, int $id): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);

        $cartItem = $cart->items()->where('id', $id)->firstOrFail();

        $variant = $cartItem->productVariant;
        if ($request->quantity > $variant->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.',
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cart->load(['items.productVariant.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated.',
            'data'    => new CartResource($cart),
        ]);
    }


    public function destroy(Request $request, int $id): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);

        $cartItem = $cart->items()->where('id', $id)->firstOrFail();
        $cartItem->delete();

        $cart->load(['items.productVariant.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'data'    => new CartResource($cart),
        ]);
    }


    public function clear(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
            'data'    => [
                'id'             => $cart->id,
                'total_quantity' => 0,
                'total_price'    => 0,
                'items'          => [],
            ],
        ]);
    }

    // -------------------------------------------------------
    // PRIVATE HELPER — get or create cart for current user
    // -------------------------------------------------------
    private function getOrCreateCart(Request $request): Cart
    {
        $user = $request->user();

        if ($user) {
            // Logged in user — get or create by user_id
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // If user had a guest cart — merge it
            $sessionId = $request->header('X-Session-ID');
            if ($sessionId) {
                $guestCart = Cart::where('session_id', $sessionId)
                                 ->whereNull('user_id')
                                 ->first();
                if ($guestCart) {
                    $cart->mergeWith($guestCart);
                }
            }

            return $cart;
        }

        // Guest user — get or create by session ID
        $sessionId = $request->header('X-Session-ID');

        if (!$sessionId) {
            // Generate a new session ID if none provided
            $sessionId = \Illuminate\Support\Str::uuid()->toString();
        }

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }
}