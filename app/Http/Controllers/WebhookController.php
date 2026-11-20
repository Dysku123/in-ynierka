<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Enums\PaymentStatus;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $signature, config('services.stripe.webhook_secret'));
        } catch (\Throwable $e) {
            return response('Nieprawidłowy podpis', 400);
        }
        if ($event->type === 'checkout.session.completed') {
            $orderId = $event->data->object->metadata->order_id ?? null;
            $order = Order::find($orderId);
            if ($order && $order->payment_status === PaymentStatus::Unpaid) {
                $order->update(['payment_status' => PaymentStatus::Paid]);
            }
        }
        return response('ok', 200);
    }
}
