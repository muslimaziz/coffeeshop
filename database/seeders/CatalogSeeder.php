<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Recipe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Seed categories, products, variants, ingredients and recipes.
     */
    public function run(): void
    {
        $coffee = Category::updateOrCreate(
            ['slug' => 'kopi'],
            ['nama' => 'Kopi', 'deskripsi' => 'Minuman berbasis espresso', 'is_active' => true]
        );
        $nonCoffee = Category::updateOrCreate(
            ['slug' => 'non-kopi'],
            ['nama' => 'Non-Kopi', 'deskripsi' => 'Minuman tanpa kafein', 'is_active' => true]
        );
        $pastries = Category::updateOrCreate(
            ['slug' => 'pastry'],
            ['nama' => 'Pastry', 'deskripsi' => 'Roti dan kue', 'is_active' => true]
        );

        $ingredients = collect([
            ['nama' => 'Espresso Shot', 'satuan' => 'ml', 'stok' => 5000, 'min' => 500],
            ['nama' => 'Susu Segar', 'satuan' => 'ml', 'stok' => 20000, 'min' => 2000],
            ['nama' => 'Gula Aren', 'satuan' => 'gram', 'stok' => 3000, 'min' => 500],
            ['nama' => 'Biji Kopi Arabika', 'satuan' => 'gram', 'stok' => 8000, 'min' => 1000],
            ['nama' => 'Cokelat Bubuk', 'satuan' => 'gram', 'stok' => 4000, 'min' => 500],
            ['nama' => 'Susu Oat', 'satuan' => 'ml', 'stok' => 6000, 'min' => 1000],
            ['nama' => 'Sirup Vanila', 'satuan' => 'ml', 'stok' => 2500, 'min' => 300],
            ['nama' => 'Tepung Terigu', 'satuan' => 'gram', 'stok' => 10000, 'min' => 2000],
            ['nama' => 'Butter', 'satuan' => 'gram', 'stok' => 5000, 'min' => 500],
            ['nama' => 'Matcha Bubuk', 'satuan' => 'gram', 'stok' => 2000, 'min' => 300],
        ])->mapWithKeys(fn (array $i) => [
            $i['nama'] => Ingredient::updateOrCreate(
                ['nama' => $i['nama']],
                ['satuan' => $i['satuan'], 'stok_saat_ini' => $i['stok'], 'stok_minimum' => $i['min']]
            ),
        ]);

        $products = [
            ['nama' => 'Signature Latte', 'category' => $coffee, 'harga' => 28000, 'desc' => 'Espresso dengan susu segar yang creamy.', 'ingredients' => ['Espresso Shot' => 60, 'Susu Segar' => 180, 'Sirup Vanila' => 10]],
            ['nama' => 'Caffè Mocha', 'category' => $coffee, 'harga' => 32000, 'desc' => 'Espresso, cokelat, dan susu segar.', 'ingredients' => ['Espresso Shot' => 60, 'Susu Segar' => 150, 'Cokelat Bubuk' => 20]],
            ['nama' => 'Iced Americano', 'category' => $coffee, 'harga' => 22000, 'desc' => 'Espresso dengan air dingin dan es.', 'ingredients' => ['Espresso Shot' => 90, 'Biji Kopi Arabika' => 20]],
            ['nama' => 'Aren Latte', 'category' => $coffee, 'harga' => 30000, 'desc' => 'Latte dengan gula aren khas.', 'ingredients' => ['Espresso Shot' => 60, 'Susu Segar' => 150, 'Gula Aren' => 25]],
            ['nama' => 'Cappuccino', 'category' => $coffee, 'harga' => 28000, 'desc' => 'Espresso, susu, dan busa susu yang tebal.', 'ingredients' => ['Espresso Shot' => 60, 'Susu Segar' => 120]],
            ['nama' => 'Iced Matcha Latte', 'category' => $nonCoffee, 'harga' => 30000, 'desc' => 'Matcha premium dengan susu segar.', 'ingredients' => ['Matcha Bubuk' => 12, 'Susu Segar' => 200]],
            ['nama' => 'Chocolate Milk', 'category' => $nonCoffee, 'harga' => 26000, 'desc' => 'Susu segar dengan cokelat.', 'ingredients' => ['Susu Segar' => 250, 'Cokelat Bubuk' => 25]],
            ['nama' => 'Vanilla Latte (Oat)', 'category' => $nonCoffee, 'harga' => 34000, 'desc' => 'Latte vanila dengan susu oat.', 'ingredients' => ['Espresso Shot' => 60, 'Susu Oat' => 180, 'Sirup Vanila' => 15]],
            ['nama' => 'Butter Croissant', 'category' => $pastries, 'harga' => 18000, 'desc' => 'Croissant renyah dengan butter.', 'ingredients' => ['Tepung Terigu' => 80, 'Butter' => 30]],
            ['nama' => 'Cinnamon Roll', 'category' => $pastries, 'harga' => 20000, 'desc' => 'Roti gulung kayu manis.', 'ingredients' => ['Tepung Terigu' => 100, 'Butter' => 25, 'Gula Aren' => 20]],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($data['nama'])],
                [
                    'category_id' => $data['category']->id,
                    'nama' => $data['nama'],
                    'deskripsi' => $data['desc'],
                    'harga_dasar' => $data['harga'],
                    'is_active' => true,
                    'is_new' => in_array($data['nama'], ['Iced Matcha Latte', 'Cinnamon Roll']),
                ]
            );

            foreach ($data['ingredients'] as $name => $qty) {
                Recipe::updateOrCreate(
                    ['product_id' => $product->id, 'ingredient_id' => $ingredients[$name]->id],
                    ['jumlah_terpakai' => $qty]
                );
            }

            $variantSets = [
                ['tipe' => 'size', 'nama' => 'Small', 'harga' => 0],
                ['tipe' => 'size', 'nama' => 'Medium', 'harga' => 4000],
                ['tipe' => 'size', 'nama' => 'Large', 'harga' => 8000],
                ['tipe' => 'sugar', 'nama' => 'Less Sugar', 'harga' => 0],
                ['tipe' => 'sugar', 'nama' => 'Normal', 'harga' => 0],
                ['tipe' => 'milk', 'nama' => 'Susu Segar', 'harga' => 0],
                ['tipe' => 'milk', 'nama' => 'Susu Oat', 'harga' => 6000],
                ['tipe' => 'topping', 'nama' => 'Extra Shot', 'harga' => 5000],
                ['tipe' => 'topping', 'nama' => 'Whipped Cream', 'harga' => 4000],
            ];

            if ($data['category']->id === $pastries->id) {
                $variantSets = array_filter($variantSets, fn (array $v) => $v['tipe'] === 'topping');
            }

            foreach ($variantSets as $variant) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'tipe' => $variant['tipe'],
                        'nama' => $variant['nama'],
                    ],
                    ['harga_tambahan' => $variant['harga'], 'is_active' => true]
                );
            }
        }
    }
}
