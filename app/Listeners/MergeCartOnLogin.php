<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\CartService;

class MergeCartOnLogin
{
    public function __construct(private CartService $cartService) {}

    public function handle(Login $event): void
    {
        $sessionId = session()->getId();
        $userId = $event->user->id;

        $this->cartService->mergeSessionCart($userId, $sessionId);
    }
}
