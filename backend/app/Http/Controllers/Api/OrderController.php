<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
                       ->withCount('items')
                       ->latest()
                       ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders),
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }


    public function show(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
                      ->where('user_id', $request->user()->id)
                      ->with('items')
                      ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new OrderResource($order),
        ]);
    }

    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $user = $request->user();

        $cart = Cart::where('user_id', $user->id)
                    ->with(['items.productVariant.product.images'])
                    ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ], 422);
        }

        foreach ($cart->items as $item) {
            if ($item->quantity > $item->productVariant->stock) {
                return response()->json([
                    'success' => false,
                    'message' => $item->productVariant->product->name
                                 . ' (' . $item->productVariant->size . ')'
                                 . ' does not have enough stock.',
                ], 422);
            }
        }

        $order = DB::transaction(function () use ($request, $user, $cart) {

            $subtotal       = $cart->total_price;
            $shippingCharge = $subtotal >= 5000 ? 0 : 299; // free shipping above 5000
            $discountAmount = 0;
            $totalAmount    = $subtotal + $shippingCharge - $discountAmount;

            $order = Order::create([
                'user_id'          => $user->id,
                'status'           => 'pending',
                'payment_status'   => $request->payment_method === 'cod' ? 'pending' : 'paid',
                'payment_method'   => $request->payment_method,
                'payment_id'       => $request->payment_id,
                'razorpay_order_id'=> $request->razorpay_order_id,
                'subtotal'         => $subtotal,
                'discount_amount'  => $discountAmount,
                'shipping_charge'  => $shippingCharge,
                'total_amount'     => $totalAmount,
                'shipping_address' => $request->shipping_address,
                'customer_notes'   => $request->customer_notes,
            ]);

            // Create order items from cart — with snapshots
            foreach ($cart->items as $item) {
                $variant = $item->productVariant;
                $product = $variant->product;

                $primaryImage = $product->images
                                        ->where('is_primary', true)
                                        ->first();

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name'       => $product->name,
                    'product_sku'        => $variant->sku,
                    'variant_size'       => $variant->size,
                    'product_image'      => $primaryImage?->image_path,
                    'quantity'           => $item->quantity,
                    'unit_price'         => $variant->price,
                    'subtotal'           => $item->quantity * $variant->price,
                ]);

                // Decrement stock
                $variant->decrement('stock', $item->quantity);
            }

            // Clear the cart after order is placed
            $cart->items()->delete();

            return $order;
        });

  
        $order->load('items');

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully.',
            'data'    => new OrderResource($order),
        ], 201);
    }


    public function cancel(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
                      ->where('user_id', $request->user()->id)
                      ->firstOrFail();

        if (!$order->isCancellable()) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled.',
            ], 422);
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->productVariant) {
                    $item->productVariant->increment('stock', $item->quantity);
                }
            }

       
            $order->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
            'data'    => new OrderResource($order->fresh()),
        ]);
    }
}