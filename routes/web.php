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

Route::get('/shop', 'Shop@index')->name('shop');
Route::get('/shop/customer-search', 'Shop@customerSearch')->name('shop.customerSearch');
Route::get('/shop/product-search', 'Shop@productSearch')->name('shop.productSearch');

Route::get('/products', 'Product@index')->name('products');
Route::get('/products/edit/{id}', 'Product@edit')->name('products.edit');
Route::post('/products/update/{id}', 'Product@update')->name('products.update');
Route::get('/products/create', 'Product@create')->name('products.create');
Route::post('/products/store', 'Product@store')->name('products.store');
Route::get('/products/destroy/{id}', 'Product@destroy')->name('products.destroy');

Route::get('/customers', 'Customer@index')->name('customers');
Route::get('/customers/edit/{id}', 'Customer@edit')->name('customers.edit');
Route::post('/customers/update/{id}', 'Customer@update')->name('customers.update');
Route::get('/customers/create', 'Customer@create')->name('customers.create');
Route::post('/customers/store', 'Customer@store')->name('customers.store');
Route::get('/customers/destroy/{id}', 'Customer@destroy')->name('customers.destroy');
