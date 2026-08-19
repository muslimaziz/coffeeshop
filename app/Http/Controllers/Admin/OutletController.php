<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOutletRequest;
use App\Http\Requests\Admin\UpdateOutletRequest;
use App\Models\Outlet;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $outlets = Outlet::withCount('users')->orderBy('nama')->paginate(15);

        return view('admin.outlets.index', compact('outlets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.outlets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOutletRequest $request): RedirectResponse
    {
        Outlet::create(array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Outlet $outlet): View
    {
        return view('admin.outlets.edit', compact('outlet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOutletRequest $request, Outlet $outlet): RedirectResponse
    {
        $outlet->update(array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Outlet $outlet): RedirectResponse
    {
        if ($outlet->users()->count() > 0 || $outlet->orders()->count() > 0) {
            return back()->with('error', 'Outlet masih memiliki data terkait.');
        }

        $outlet->delete();

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet berhasil dihapus.');
    }
}
