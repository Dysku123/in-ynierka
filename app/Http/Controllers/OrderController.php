<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;

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
