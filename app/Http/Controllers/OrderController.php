<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller{
    public function store(Request $request, OrderService $orderService){
       $userId = $request->user()?->id;
       $sessionId = session()->getId();

       $order = $orderService->createOrderFromCart($userId, $sessionId);


       return redirect()->route('home')->with('success', 'Zamówienie złożone pomyślnie! Twoje ID zamówienia to: ' . $order->id);
    }

}

