<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Daily sales totals for the last N days (today first).
     *
     * @return Collection<int, array{date: string, total: int, count: int}>
     */
    public function dailySales(int $days = 7): Collection
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $rows = Order::where('created_at', '>=', $from)
            ->whereIn('status', ['selesai', 'diantar'])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        return collect(range(0, $days - 1))->map(function (int $offset) use ($rows) {
            $date = now()->subDays($offset)->format('Y-m-d');

            return [
                'date' => $date,
                'total' => (int) ($rows[$date]->total ?? 0),
                'count' => (int) ($rows[$date]->count ?? 0),
            ];
        })->reverse()->values();
    }

    /**
     * Best-selling products, optionally scoped to a date range.
     *
     * @return Collection<int, array{product: string, qty: int, total: int}>
     */
    public function topProducts(?Carbon $from = null, ?Carbon $to = null, int $limit = 10): Collection
    {
        return OrderItem::query()
            ->selectRaw('nama_produk as product, SUM(qty) as qty, SUM(subtotal) as total')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->groupBy('nama_produk')
            ->orderByDesc('qty')
            ->limit($limit)
            ->get();
    }

    /**
     * Sales grouped by kasir (shift) for a date range.
     *
     * @return Collection<int, array{kasir: string, count: int, total: int}>
     */
    public function salesByKasir(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return Order::query()
            ->join('users', 'orders.kasir_id', '=', 'users.id')
            ->whereIn('orders.status', ['selesai', 'diantar'])
            ->when($from, fn ($q) => $q->where('orders.created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('orders.created_at', '<=', $to))
            ->selectRaw('users.name as kasir, COUNT(*) as count, SUM(orders.total) as total')
            ->groupBy('users.name')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Simple profit/loss summary for a date range.
     *
     * @return array{revenue: int, order_count: int, avg_order: int}
     */
    public function profitLoss(?Carbon $from = null, ?Carbon $to = null): array
    {
        $orders = Order::query()
            ->whereIn('status', ['selesai', 'diantar'])
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to));

        $revenue = (int) (clone $orders)->sum('total');
        $count = (clone $orders)->count();

        return [
            'revenue' => $revenue,
            'order_count' => $count,
            'avg_order' => $count > 0 ? (int) round($revenue / $count) : 0,
        ];
    }

    /**
     * KPI summary for the admin dashboard.
     *
     * @return array{revenue: int, orders: int, customers: int, low_stock: int, today_revenue: int}
     */
    public function dashboardKpis(): array
    {
        return [
            'revenue' => (int) Order::whereIn('status', ['selesai', 'diantar'])->sum('total'),
            'orders' => (int) Order::whereIn('status', ['selesai', 'diantar'])->count(),
            'customers' => (int) User::role('customer')->count(),
            'low_stock' => app(StockService::class)->lowStock()->count(),
            'today_revenue' => (int) Order::whereDate('created_at', today())
                ->whereIn('status', ['selesai', 'diantar'])
                ->sum('total'),
        ];
    }
}
