<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Illuminate\Support\Facades\URL;

class OrderController extends Controller
{
    public function index(Request $request, OrderService $orderService)
    {
        $userId = $request->user()?->id;
        $orders = $orderService->getUserOrders($userId);
        return view('showOrders', compact('orders'));
    }

    public function showForGuest(string $uuid, OrderService $orderService)
    {
        $order = $orderService->getOrderByUuid($uuid);
        return view('guest_show', compact('order'));
    }
}
