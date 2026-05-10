<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Warranty;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // Overview counts
        $totalOrders    = Order::count();
        $totalUsers     = User::where('role', 'customer')->count();
        $totalProducts  = Product::count();
        $totalWarranties = Warranty::count();

        // Revenue
        $totalRevenue   = Order::where('payment_status', 'paid')
                               ->sum('total_amount');

        $todayRevenue   = Order::where('payment_status', 'paid')
                               ->whereDate('created_at', today())
                               ->sum('total_amount');

        $monthRevenue   = Order::where('payment_status', 'paid')
                               ->whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->sum('total_amount');

        // Order status breakdown
        $ordersByStatus = Order::selectRaw('status, count(*) as count')
                               ->groupBy('status')
                               ->pluck('count', 'status');

        // Recent orders
        $recentOrders   = Order::with('user')
                               ->latest()
                               ->take(5)
                               ->get()
                               ->map(fn($order) => [
                                   'order_number' => $order->order_number,
                                   'customer'     => $order->user->name,
                                   'total'        => $order->total_amount,
                                   'status'       => $order->status,
                                   'date'         => $order->created_at->toDateString(),
                               ]);

        // Recent users
        $recentUsers    = User::where('role', 'customer')
                              ->latest()
                              ->take(5)
                              ->get()
                              ->map(fn($user) => [
                                  'name'       => $user->name,
                                  'email'      => $user->email,
                                  'created_at' => $user->created_at->toDateString(),
                              ]);

        // Low stock variants
        $lowStock       = \App\Models\ProductVariant::with('product')
                               ->where('stock', '<=', 5)
                               ->where('is_active', true)
                               ->get()
                               ->map(fn($v) => [
                                   'product'   => $v->product->name,
                                   'size'      => $v->size,
                                   'stock' => $v->stock,
                               ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'counts' => [
                    'orders'     => $totalOrders,
                    'users'      => $totalUsers,
                    'products'   => $totalProducts,
                    'warranties' => $totalWarranties,
                ],
                'revenue' => [
                    'total'   => $totalRevenue,
                    'today'   => $todayRevenue,
                    'month'   => $monthRevenue,
                ],
                'orders_by_status' => $ordersByStatus,
                'recent_orders'    => $recentOrders,
                'recent_users'     => $recentUsers,
                'low_stock'        => $lowStock,
            ],
        ]);
    }
}