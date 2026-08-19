<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePromoRequest;
use App\Http\Requests\Admin\UpdatePromoRequest;
use App\Models\Promo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $promos = Promo::orderBy('is_active', 'desc')->orderBy('nama')->paginate(15);

        return view('admin.promos.index', compact('promos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.promos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePromoRequest $request): RedirectResponse
    {
        Promo::create(array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promo $promo): View
    {
        return view('admin.promos.edit', compact('promo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePromoRequest $request, Promo $promo): RedirectResponse
    {
        $promo->update(array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo): RedirectResponse
    {
        $promo->delete();

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}
