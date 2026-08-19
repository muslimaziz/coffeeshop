<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVariantRequest;
use App\Http\Requests\Admin\UpdateVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $product = $request->route('product');
        $variants = ProductVariant::with('product')
            ->when($product, fn ($q) => $q->where('product_id', $product->id))
            ->orderBy('product_id')
            ->paginate(15);

        return view('admin.variants.index', compact('variants', 'product'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $products = Product::active()->orderBy('nama')->get();
        $product = $request->route('product');

        return view('admin.variants.create', compact('products', 'product'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVariantRequest $request): RedirectResponse
    {
        ProductVariant::create(array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.variants.index')
            ->with('success', 'Varian berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductVariant $variant): View
    {
        $products = Product::active()->orderBy('nama')->get();

        return view('admin.variants.edit', compact('variant', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVariantRequest $request, ProductVariant $variant): RedirectResponse
    {
        $variant->update(array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.variants.index')
            ->with('success', 'Varian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $variant): RedirectResponse
    {
        $variant->delete();

        return redirect()->route('admin.variants.index')
            ->with('success', 'Varian berhasil dihapus.');
    }
}
