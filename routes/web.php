<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('role:super-admin|admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('banners', BannerController::class);
        Route::resource('products', ProductController::class);
        Route::resource('variants', VariantController::class);
        Route::resource('ingredients', IngredientController::class);
        Route::post('ingredients/{ingredient}/restock', [IngredientController::class, 'restock'])->name('ingredients.restock');
        Route::resource('recipes', RecipeController::class);
        Route::resource('promos', PromoController::class);
        Route::resource('outlets', OutletController::class);
        Route::resource('tables', TableController::class);
        Route::resource('employees', EmployeeController::class);

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'store'])->name('settings.store');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    });
});

Route::middleware(['auth', 'verified'])->prefix('pos')->name('pos.')->group(function () {
    Route::middleware('role:super-admin|admin|kasir')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('orders', [PosController::class, 'orders'])->name('orders');
        Route::get('history', [PosController::class, 'history'])->name('history');
    });
});

Route::middleware(['auth', 'verified'])->prefix('menu')->name('menu.')->group(function () {
    Route::get('/', [CustomerHomeController::class, 'index'])->name('index');
    Route::get('checkout', [CustomerOrderController::class, 'checkout'])->name('checkout');
    Route::get('orders', [CustomerOrderController::class, 'index'])->name('orders');
    Route::get('orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::get('loyalty', [CustomerOrderController::class, 'loyalty'])->name('loyalty');
});

Route::middleware(['auth', 'verified'])->prefix('kitchen')->name('kitchen.')->group(function () {
    Route::middleware('role:super-admin|admin|barista')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('index');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
