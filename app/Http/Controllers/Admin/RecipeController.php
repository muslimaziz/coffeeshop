<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRecipeRequest;
use App\Http\Requests\Admin\UpdateRecipeRequest;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $product = $request->route('product');
        $recipes = Recipe::with('product', 'ingredient')
            ->when($product, fn ($q) => $q->where('product_id', $product->id))
            ->orderBy('product_id')
            ->paginate(15);

        return view('admin.recipes.index', compact('recipes', 'product'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $products = Product::orderBy('nama')->get();
        $ingredients = Ingredient::orderBy('nama')->get();
        $product = $request->route('product');

        return view('admin.recipes.create', compact('products', 'ingredients', 'product'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecipeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $exists = Recipe::where('product_id', $data['product_id'])
            ->where('ingredient_id', $data['ingredient_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Bahan tersebut sudah ada pada produk ini.');
        }

        Recipe::create($data);

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Resep berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe): View
    {
        $products = Product::orderBy('nama')->get();
        $ingredients = Ingredient::orderBy('nama')->get();

        return view('admin.recipes.edit', compact('recipe', 'products', 'ingredients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe): RedirectResponse
    {
        $recipe->update($request->validated());

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Resep berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe): RedirectResponse
    {
        $recipe->delete();

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Resep berhasil dihapus.');
    }
}
