<?php

namespace App\Services;

use App\Models\Order;
use Stripe\StripeClient;

class PaymentService{
    public function createCheckoutSession(Order $order):string
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode'=>'payment',
            'customer_email' => $order->email,
            'line_items'=>[[
                'price_data'=>[
                    'currency' => 'pln',
                    'product_data' => ['name' => 'Zamówienie #' . $order->id],
                    'unit_amount' => $order->total_price,
                ],
                'quantity' => 1,
            ]],
            'metadata' => ['order_id' => $order->id],
            'success_url' => route('checkout.success', ['order' =>$order->id]),
            'cancel_url' => route('checkout.cancel', ['order' => $order->id]),
        ]);

        return $session->url;
    }
}
