<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();
Route::get('/', 'HomeController@index')->name('home');

Route::get('/cash', 'Cash@index')->name('cash');
Route::get('/products', 'Products@index')->name('products');

Route::get('/customer', 'Customer@index')->name('customer');
Route::get('/customer/edit/{id}', 'Customer@edit')->name('customer.edit');
Route::post('/customer/update/{id}', 'Customer@update')->name('customer.update');
Route::get('/customer/create', 'Customer@create')->name('customer.create');
Route::post('/customer/store', 'Customer@store')->name('customer.store');
Route::get('/customer/destroy/{id}', 'Customer@destroy')->name('customer.destroy');
