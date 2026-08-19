<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Services\StockService;
use Illuminate\Support\Facades\Log;

class DeductStock
{
    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        $items = $event->order->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'qty' => $item->qty,
        ])->all();

        if (empty($items)) {
            return;
        }

        $result = app(StockService::class)->deductForItems($items);

        if (! $result['ok']) {
            Log::warning('Stok gagal dikurangi untuk order '.$event->order->kode_order, $result['errors']);
        }
    }
}
