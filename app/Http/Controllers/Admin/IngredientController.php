<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIngredientRequest;
use App\Http\Requests\Admin\UpdateIngredientRequest;
use App\Models\Ingredient;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $ingredients = Ingredient::orderBy('nama')->paginate(15);
        $lowStock = app(StockService::class)->lowStock()->pluck('id');

        return view('admin.ingredients.index', compact('ingredients', 'lowStock'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.ingredients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIngredientRequest $request): RedirectResponse
    {
        Ingredient::create($request->validated());

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient): View
    {
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $ingredient->update($request->validated());

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    /**
     * Restock an ingredient.
     */
    public function restock(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $request->validate(['jumlah' => ['required', 'numeric', 'gt:0']]);

        app(StockService::class)->restock($ingredient, (float) $request->jumlah);

        return back()->with('success', 'Stok '.$ingredient->nama.' berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        $ingredient->delete();

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }
}
