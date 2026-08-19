<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Promo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function checkout(): View|RedirectResponse
    {
        $cart = session()->get('customer_cart', []);

        if (empty($cart)) {
            return redirect()->route('menu.index');
        }

        return view('customer.checkout', [
            'outlets' => Outlet::orderBy('nama')->get(),
            'promos' => Promo::where('is_active', true)->get(),
        ]);
    }

    /**
     * List the customer's orders.
     */
    public function index(Request $request): View
    {
        $orders = Order::with('items', 'payments')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('customer.orders', ['orders' => $orders]);
    }

    /**
     * Show a single order detail / tracking.
     */
    public function show(Request $request, Order $order): View
    {
        if ($order->user_id !== $request->user()->id && ! $request->user()->hasAnyRole(['super-admin', 'admin', 'kasir', 'barista'])) {
            abort(403);
        }

        return view('customer.order-show', ['order' => $order->load('items', 'payments', 'shift')]);
    }

    /**
     * Show loyalty points page.
     */
    public function loyalty(): View
    {
        return view('customer.loyalty');
    }
}
