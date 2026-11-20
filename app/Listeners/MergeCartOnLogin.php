<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\CartService;

class MergeCartOnLogin
{
    public function __construct(private CartService $cartService) {}

    public function handle(Login $event): void
    {
        $guestSessionId = session()->pull('guest_cart_session_id');

        if ($guestSessionId) {
            $this->cartService->mergeSessionCart($event->user->id, $guestSessionId);
        }
    }
}
