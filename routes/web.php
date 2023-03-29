<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User;
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
    Route::get('/', function () { return redirect()->route('dashboard'); });
    Route::get('/dashboard', [\App\Http\Controllers\Dashboard::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/retails', [Retail::class, 'index'])->name('retails.list');
    Route::get('/retails/create', [Retail::class, 'create'])->name('retails.create');
    Route::post('/retails/store', [Retail::class, 'store'])->name('retails.store');
    Route::get('/retails/edit/{id}', [Retail::class, 'edit'])->name('retails.edit');
    Route::post('/retails/update/{id}', [Retail::class, 'update'])->name('retails.update');
    Route::get('/retails/destroy/{id}', [Retail::class, 'destroy'])->name('retails.destroy');

    Route::get('/users', [User::class, 'index'])->name('users.list');
    Route::get('/users/create', [User::class, 'create'])->name('users.create');
    Route::post('/users/store', [User::class, 'store'])->name('users.store');
    Route::get('/users/edit/{id}', [User::class, 'edit'])->name('users.edit');
    Route::post('/users/update/{id}', [User::class, 'update'])->name('users.update');
    Route::get('/users/destroy/{id}', [User::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
