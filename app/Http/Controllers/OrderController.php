<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Illuminate\Support\Facades\URL;

class OrderController extends Controller
{
    public function store(Request $request, OrderService $orderService)
    {
        $userId = $request->user()?->id;

        if (!$userId) {
            $request->validate([
                'email' => 'required|email',
            ]);
        }
        $sessionId = session()->getId();
        $email = $request->user()?->email ?? $request->input('email');
        try {
            $order = $orderService->createOrderFromCart($userId, $sessionId, $email);
        } catch (\Exception $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }
        if (isset($userId)) {
            return redirect()->route('orders.index')->with('success', 'Zamówienie złożone pomyślnie! Twoje ID zamówienia to: ' . $order->id);
        }
        $url = URL::signedRoute('order.guest_show', ['uuid' => $order->uuid]);
        return redirect()->to($url)->with('success', 'Zamówienie złożone pomyślnie!');
    }

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
