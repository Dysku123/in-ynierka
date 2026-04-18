<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function store(Request $request, OrderService $orderService)
    {
        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        $email = $request->user()?->email ?? $request->input('email');
        $order = $orderService->createOrderFromCart($userId, $sessionId, $email);

        if(isset($userId)){
            return redirect()->route('orders.index')->with('success', 'Zamówienie złożone pomyślnie! Twoje ID zamówienia to: ' . $order->id);
        } else{
            return redirect()->route('order.guest_show', ['uuid' => $order->uuid])->with('success', 'Zamówienie złożone pomyślnie! Twoje ID zamówienia to: ' . $order->id);
        }

    }

    public function index(Request $request, OrderService $orderService){
        $userId = $request->user()?->id;
        $orders= $orderService->getUserOrders($userId);
        return view('showOrders', compact('orders'));
    }

    public function showForGuest(string $uuid, OrderService $orderService){
        $order = $orderService->getOrderByUuid($uuid);
        return view('guest_show', compact('order'));
    }
}
