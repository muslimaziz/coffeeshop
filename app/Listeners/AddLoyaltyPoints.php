<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Services\LoyaltyService;

class AddLoyaltyPoints
{
    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        app(LoyaltyService::class)->addPointsForOrder($event->order);
    }
}
