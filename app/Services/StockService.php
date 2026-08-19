<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Support\Collection;

class StockService
{
    /**
     * Attempt to deduct ingredient stock for the given order items.
     * Uses a transaction so nothing is deducted unless everything succeeds.
     *
     * @return array<string, mixed> result with 'ok' boolean and optional 'errors'
     */
    public function deductForItems(array $items): array
    {
        $requirements = $this->requirementsForItems($items);

        if ($requirements['missing']) {
            return ['ok' => false, 'errors' => $requirements['missing']];
        }

        foreach ($requirements['ingredients'] as $ingredient) {
            $ingredient->decrement('stok_saat_ini', $ingredient->jumlah_terpakai);
        }

        return ['ok' => true];
    }

    /**
     * Restore ingredient stock for the given order items (e.g. on cancellation).
     */
    public function restoreForItems(array $items): void
    {
        $requirements = $this->requirementsForItems($items);

        foreach ($requirements['ingredients'] as $ingredient) {
            $ingredient->increment('stok_saat_ini', $ingredient->jumlah_terpakai);
        }
    }

    /**
     * Compute required ingredient amounts and detect insufficient stock.
     *
     * @return array{ingredients: Collection, missing: array<int, string>}
     */
    public function requirementsForItems(array $items): array
    {
        $totals = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (! $product) {
                continue;
            }

            foreach ($product->recipes as $recipe) {
                $ingredientId = $recipe->ingredient_id;
                $totals[$ingredientId] = ($totals[$ingredientId] ?? 0) + ($recipe->jumlah_terpakai * $item['qty']);
            }
        }

        $ingredients = Ingredient::whereIn('id', array_keys($totals))->get();
        $missing = [];

        foreach ($ingredients as $ingredient) {
            $needed = $totals[$ingredient->id];
            if ($ingredient->stok_saat_ini < $needed) {
                $missing[] = "Stok {$ingredient->nama} tidak mencukupi ({$ingredient->stok_saat_ini} < {$needed}).";
            }

            $ingredient->jumlah_terpakai = $needed;
        }

        return ['ingredients' => $ingredients, 'missing' => $missing];
    }

    /**
     * List ingredients whose stock is at or below the minimum threshold.
     */
    public function lowStock(): Collection
    {
        return Ingredient::whereColumn('stok_saat_ini', '<=', 'stok_minimum')->get();
    }

    /**
     * Restock an ingredient by a given amount.
     */
    public function restock(Ingredient $ingredient, float $amount): Ingredient
    {
        $ingredient->increment('stok_saat_ini', $amount);

        return $ingredient->fresh();
    }
}
