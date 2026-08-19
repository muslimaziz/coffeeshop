<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTableRequest;
use App\Http\Requests\Admin\UpdateTableRequest;
use App\Models\Outlet;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tables = Table::with('outlet')->orderBy('nomor_meja')->paginate(15);

        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $outlets = Outlet::orderBy('nama')->get();

        return view('admin.tables.create', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTableRequest $request): RedirectResponse
    {
        Table::create($request->validated());

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table): View
    {
        $outlets = Outlet::orderBy('nama')->get();

        return view('admin.tables.edit', compact('table', 'outlets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTableRequest $request, Table $table): RedirectResponse
    {
        $table->update($request->validated());

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table): RedirectResponse
    {
        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil dihapus.');
    }
}
