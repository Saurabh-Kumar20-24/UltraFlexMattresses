<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // GET /api/admin/orders
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q) =>
                      $q->where('name', 'like', '%' . $request->search . '%')
                  );
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->paginate(15);

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

    // PATCH /api/admin/orders/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status'            => 'sometimes|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'payment_status'    => 'sometimes|in:pending,paid,failed,refunded',
            'tracking_number'   => 'nullable|string|max:100',
            'shipping_provider' => 'nullable|string|max:100',
            'admin_notes'       => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'status',
            'payment_status',
            'tracking_number',
            'shipping_provider',
            'admin_notes',
        ]);

        // Set timestamps based on status
        if ($request->status === 'shipped') {
            $data['shipped_at'] = now();
        }

        if ($request->status === 'delivered') {
            $data['delivered_at'] = now();
        }

        if ($request->status === 'cancelled') {
            $data['cancelled_at'] = now();
        }

        $order->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.',
            'data'    => new OrderResource($order->fresh()->load('items')),
        ]);
    }
}