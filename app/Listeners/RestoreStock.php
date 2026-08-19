<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Services\StockService;

class RestoreStock
{
    /**
     * Handle the event.
     */
    public function handle(OrderCancelled $event): void
    {
        $items = $event->order->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'qty' => $item->qty,
        ])->all();

        if (empty($items)) {
            return;
        }

        app(StockService::class)->restoreForItems($items);
    }
}
