<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Support\Facades\Log;

class SendOrderNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        Log::info('Pesanan selesai: '.$event->order->kode_order);
    }
}
