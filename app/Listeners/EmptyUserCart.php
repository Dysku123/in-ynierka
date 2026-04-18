<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Services\CartService;
use Illuminate\Support\Facades\Session;

class EmptyUserCart
{
    public function __construct(public CartService $cartService)
    {
    }

    public function handle(OrderCreated $event): void
    {

        $sessionId = Session::getId();
        $this->cartService->clearCart($sessionId);
    }
}
