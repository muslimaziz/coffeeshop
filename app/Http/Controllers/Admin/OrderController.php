<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderCompleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $orders = Order::with('user', 'kasir', 'outlet')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->q, fn ($q) => $q->where('kode_order', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        $order->load('items', 'payments', 'user', 'kasir', 'outlet', 'promo');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $order->update(['status' => $request->status]);

        if (in_array($request->status, ['selesai', 'diantar']) && $order->status !== $request->status) {
            OrderCompleted::dispatch($order);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil dihapus.');
    }
}
