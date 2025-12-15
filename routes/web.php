<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Shop;
use App\Http\Controllers\Store;
use App\Http\Controllers\User;
use App\Http\Controllers\Order;
use App\Http\Controllers\Product;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Job;
use App\Http\Controllers\Retail;
use App\Http\Controllers\Setting;
use App\Http\Controllers\Report;
use \App\Http\Controllers\ModJobsSettings;
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
    Route::get('/shop/points-half/{id}', [Shop::class, 'points_half'])->name('shop.points_half');
    Route::post('/shop/store', [Shop::class, 'store'])->name('shop.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/orders', [Order::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{id}', [Order::class, 'show'])->name('orders.show');
//    Route::get('/orders/create', [Order::class, 'create'])->name('orders.create');
//    Route::post('/orders/store', [Order::class, 'store'])->name('orders.store');
    Route::get('/orders/edit_alert/{id}', [Order::class, 'edit_alert'])->name('orders.edit_alert');
    Route::get('/orders/edit/{id}', [Order::class, 'edit'])->name('orders.edit');
//    Route::post('/orders/update/{id}', [Order::class, 'update'])->name('orders.update');
    Route::get('/orders/destroy/{id}', [Order::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/download', [Order::class, 'download'])->name('orders.download');

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

    Route::get('/customers', [Customer::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [Customer::class, 'create'])->name('customers.create');
    Route::post('/customers/store', [Customer::class, 'store'])->name('customers.store');
    Route::get('/customers/edit/{id}', [Customer::class, 'edit'])->name('customers.edit');
    Route::post('/customers/update/{id}', [Customer::class, 'update'])->name('customers.update');
    Route::get('/customers/destroy/{id}', [Customer::class, 'destroy'])->name('customers.destroy');
    Route::get('/customers/active/{id}', [Customer::class, 'active'])->name('customers.active');
    Route::get('/customers/view_reception/{id}', [Customer::class, 'view_reception'])->name('customers.view_reception');

    Route::get('/jobs', [Job::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [Job::class, 'create'])->name('jobs.create');
    Route::post('/jobs/store', [Job::class, 'store'])->name('jobs.store');
    Route::get('/jobs/edit/{id}', [Job::class, 'edit'])->name('jobs.edit');
    Route::post('/jobs/update/{id}', [Job::class, 'update'])->name('jobs.update');
    Route::get('/jobs/destroy/{id}', [Job::class, 'destroy'])->name('jobs.destroy');


    Route::get('/jobs-settings/sections', [ModJobsSettings::class, 'indexSections'])
        ->name('jobs_settings.index');
    Route::get('/jobs-settings/sections/create', [ModJobsSettings::class, 'createSections'])
        ->name('jobs_settings.sections.create');
    Route::post('/jobs-settings/sections/store', [ModJobsSettings::class, 'storeSections'])
        ->name('jobs_settings.sections.store');
    Route::get('/jobs-settings/sections/edit/{id}', [ModJobsSettings::class, 'editSections'])
        ->name('jobs_settings.sections.edit');
    Route::post('/jobs-settings/sections/update/{id}', [ModJobsSettings::class, 'updateSections'])
        ->name('jobs_settings.sections.update');
    Route::get('/jobs-settings/sections/destroy/{id}', [ModJobsSettings::class, 'destroySections'])
        ->name('jobs_settings.sections.destroy');

    Route::get('/jobs-settings/reports', [ModJobsSettings::class, 'indexReports'])
        ->name('jobs_settings.reports.index');
    Route::get('/jobs-settings/reports/create', [ModJobsSettings::class, 'createReports'])
        ->name('jobs_settings.reports.create');
    Route::post('/jobs-settings/reports/store', [ModJobsSettings::class, 'storeReports'])
        ->name('jobs_settings.reports.store');
    Route::get('/jobs-settings/reports/edit/{id}', [ModJobsSettings::class, 'editReports'])
        ->name('jobs_settings.reports.edit');
    Route::post('/jobs-settings/reports/update/{id}', [ModJobsSettings::class, 'updateReports'])
        ->name('jobs_settings.reports.update');
    Route::get('/jobs-settings/reports/destroy/{id}', [ModJobsSettings::class, 'destroyReports'])
        ->name('jobs_settings.reports.destroy');


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

    Route::get('/settings', [Setting::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [Setting::class, 'update'])->name('settings.update');
    Route::get('/settings/db2excel', [Setting::class, 'db2Excel_web'])->name('settings.db2excel');

    Route::get('/report', [Report::class, 'index'])->name('report.index');
    Route::get('/report/send', [Report::class, 'mailSendWeb'])->name('report.sendmail');
});

require __DIR__.'/auth.php';
