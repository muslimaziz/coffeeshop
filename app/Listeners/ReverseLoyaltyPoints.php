<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Services\LoyaltyService;

class ReverseLoyaltyPoints
{
    /**
     * Handle the event.
     */
    public function handle(OrderCancelled $event): void
    {
        app(LoyaltyService::class)->reverseForOrder($event->order);
    }
}
