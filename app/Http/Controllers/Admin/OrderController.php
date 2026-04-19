<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Enums\OrderStatus; // do sprawdzania enmuma
use Exception; // do weryfikacji, czy musze to kazdorazowo imporotwac, czy mozna raz na projekt
use App\Models\Order;

class OrderController extends Controller
{

    public function updateStatus(Request $request, Order $order, OrderService $orderService)
    {
        $validated = $request->validate([
            'status' => ['required', \Illuminate\Validation\Rule::enum(OrderStatus::class)], // spprawdzamy czy nasz status jest zdefinowany w naszej liscie enumów
        ]);
        $status = OrderStatus::from($validated['status']);

        $orderService->changeStatus($order->id, $status);

        return redirect()->back()->with('success', 'Status zamówienia został zaktualizowany!');
    }

    public function show(Order $order)
    {
        $order->load('items'); // ładujemy relację z pozycjami zamówienia
        return view('admin.orders.show', compact('order'));
    }

}
