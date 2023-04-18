<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Shop;
use App\Http\Controllers\Store;
use App\Http\Controllers\User;
use App\Http\Controllers\Order;
use App\Http\Controllers\Product;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Retail;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});*/

Route::middleware('auth')->group(function () {
    Route::get('/', function () { return redirect()->route('dashboard.index'); });
    Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard.index');

    Route::any('/shop', [Shop::class, 'index'])->name('shop.index');
    Route::get('/shop/remove/{id}', [Shop::class, 'remove'])->name('shop.remove');
    Route::post('/shop/store', [Shop::class, 'store'])->name('shop.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/orders', [Order::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{id}', [Order::class, 'show'])->name('orders.show');
    /*Route::get('/orders/create', [Order::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [Order::class, 'store'])->name('orders.store');
    Route::get('/orders/edit/{id}', [Order::class, 'edit'])->name('orders.edit');
    Route::post('/orders/update/{id}', [Order::class, 'update'])->name('orders.update');
    Route::get('/orders/destroy/{id}', [Order::class, 'destroy'])->name('orders.destroy');*/

    Route::get('/store', [Store::class, 'index'])->name('store.index');
    Route::post('/store/store/{id}', [Store::class, 'store'])->name('store.store');

    Route::get('/products', [Product::class, 'index'])->name('products.index');
    Route::get('/products/create', [Product::class, 'create'])->name('products.create');
    Route::post('/products/store', [Product::class, 'store'])->name('products.store');
    Route::get('/products/edit/{id}', [Product::class, 'edit'])->name('products.edit');
    Route::post('/products/update/{id}', [Product::class, 'update'])->name('products.update');
    Route::get('/products/destroy/{id}', [Product::class, 'destroy'])->name('products.destroy');

    Route::get('/products/categories', [Product::class, 'category_index'])->name('products.categories.index');
    Route::get('/products/categories/create', [Product::class, 'category_create'])->name('products.categories.create');
    Route::post('/products/categories/store', [Product::class, 'category_store'])->name('products.categories.store');
    Route::get('/products/categories/edit/{id}', [Product::class, 'category_edit'])->name('products.categories.edit');
    Route::post('/products/categories/update/{id}', [Product::class, 'category_update'])->name('products.categories.update');
    Route::get('/products/categories/destroy/{id}', [Product::class, 'category_destroy'])->name('products.categories.destroy');

    Route::get('/products/box', [Product::class, 'box_index'])->name('products.box.index');
    Route::get('/products/box/create', [Product::class, 'box_create'])->name('products.box.create');
    Route::post('/products/box/store', [Product::class, 'box_store'])->name('products.box.store');
    Route::get('/products/box/edit/{id}', [Product::class, 'box_edit'])->name('products.box.edit');
    Route::post('/products/box/update/{id}', [Product::class, 'box_update'])->name('products.box.update');
    Route::get('/products/box/destroy/{id}', [Product::class, 'box_destroy'])->name('products.box.destroy');
    Route::get('/products/box/add_to_box/{id}', [Product::class, 'add_to_box'])->name('products.box.addToBox');

    Route::get('/customers', [Customer::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [Customer::class, 'create'])->name('customers.create');
    Route::post('/customers/store', [Customer::class, 'store'])->name('customers.store');
    Route::get('/customers/edit/{id}', [Customer::class, 'edit'])->name('customers.edit');
    Route::post('/customers/update/{id}', [Customer::class, 'update'])->name('customers.update');
    Route::get('/customers/destroy/{id}', [Customer::class, 'destroy'])->name('customers.destroy');
    Route::get('/customers/active/{id}', [Customer::class, 'active'])->name('customers.active');

    Route::get('/retails', [Retail::class, 'index'])->name('retails.index');
    Route::get('/retails/create', [Retail::class, 'create'])->name('retails.create');
    Route::post('/retails/store', [Retail::class, 'store'])->name('retails.store');
    Route::get('/retails/edit/{id}', [Retail::class, 'edit'])->name('retails.edit');
    Route::post('/retails/update/{id}', [Retail::class, 'update'])->name('retails.update');
    Route::get('/retails/destroy/{id}', [Retail::class, 'destroy'])->name('retails.destroy');

    Route::get('/users', [User::class, 'index'])->name('users.index');
    Route::get('/users/create', [User::class, 'create'])->name('users.create');
    Route::post('/users/store', [User::class, 'store'])->name('users.store');
    Route::get('/users/edit/{id}', [User::class, 'edit'])->name('users.edit');
    Route::post('/users/update/{id}', [User::class, 'update'])->name('users.update');
    Route::get('/users/destroy/{id}', [User::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
