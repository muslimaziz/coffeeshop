<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(ReportService $reports): View
    {
        $kpis = $reports->dashboardKpis();
        $daily = $reports->dailySales(7);
        $recentOrders = Order::with('user', 'kasir')
            ->latest()
            ->limit(8)
            ->get();
        $topProducts = $reports->topProducts(now()->startOfWeek(), now()->endOfWeek(), 4);

        return view('admin.dashboard', compact('kpis', 'daily', 'recentOrders', 'topProducts'));
    }
}
