<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Show the POS interface.
     */
    public function index(): View
    {
        return view('pos.index');
    }

    /**
     * Show today's incoming orders across all channels for the cashier.
     */
    public function orders(): View
    {
        return view('pos.orders');
    }

    /**
     * Show today's transaction history for the cashier.
     */
    public function history(Request $request): View
    {
        $orders = Order::with('items', 'payments')
            ->where('kasir_id', $request->user()->id)
            ->whereDate('created_at', today())
            ->latest()
            ->paginate(20);

        return view('pos.history', ['orders' => $orders]);
    }
}
