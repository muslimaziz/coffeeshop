<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Show sales reports with optional date range.
     */
    public function index(Request $request): View
    {
        $from = $request->date('from')?->startOfDay();
        $to = $request->date('to')?->endOfDay();

        $reports = app(ReportService::class);

        $daily = $reports->dailySales($from && $to ? $from->diffInDays($to) + 1 : 7);
        $topProducts = $reports->topProducts($from, $to, 10);
        $byKasir = $reports->salesByKasir($from, $to);
        $profitLoss = $reports->profitLoss($from, $to);

        return view('admin.reports.index', compact('daily', 'topProducts', 'byKasir', 'profitLoss', 'from', 'to'));
    }

    /**
     * Show the stock report.
     */
    public function stock(): View
    {
        $ingredients = Ingredient::orderBy('stok_saat_ini')->get();

        return view('admin.reports.stock', compact('ingredients'));
    }
}
