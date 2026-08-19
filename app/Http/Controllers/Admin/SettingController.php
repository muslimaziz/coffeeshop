<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index(): View
    {
        $metodeBayar = ['cash' => 'Tunai', 'qris' => 'QRIS', 'kartu' => 'Kartu', 'ewallet' => 'E-Wallet'];

        return view('admin.settings.index', compact('metodeBayar'));
    }

    /**
     * Store the application settings.
     */
    public function store(StoreSettingRequest $request): RedirectResponse
    {
        Setting::set('nama_toko', $request->nama_toko ?? config('app.name'));
        Setting::set('jam_operasional', $request->jam_operasional ?? '');
        Setting::set('pajak', $request->pajak ?? 10);
        Setting::set('service_charge', $request->service_charge ?? 5);
        Setting::set('metode_bayar', json_encode($request->metode_bayar ?? ['cash', 'qris']));

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
