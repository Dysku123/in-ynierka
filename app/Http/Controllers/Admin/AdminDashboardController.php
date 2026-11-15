<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        $salesToday = Order::where('status', OrderStatus::Paid)
            ->where('created_at', '>=', $today)
            ->sum('total_price');

        $ordersCount   = Order::count();
        $pendingCount  = Order::where('status', OrderStatus::Pending)->count();
        $productsCount = Product::count();
        $lowStock      = Product::where('stock_quantity', '<', 200)->get();
        $recentOrders  = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'salesToday',
            'ordersCount',
            'pendingCount',
            'productsCount',
            'lowStock',
            'recentOrders'
        ));
    }
}
