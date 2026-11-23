<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

// ShouldQueue: mail może iść przez kolejkę, żeby nie spowalniać składania zamówienia
class SendOrderConfirmationEmail implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $email = $order->email;

        Mail::to($email)->send(new \App\Mail\OrderConfirmation($order));
    }
}
