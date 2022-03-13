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
//Route::get('/', 'HomeController@index')->name('home');
Route::get('/', 'Dashboard@index')->name('home');

Route::get('/shop', 'Shop@index')->name('shop');
Route::post('/shop/store', 'Shop@store')->name('shop.store');
Route::get('/shop/search', 'Shop@search')->name('shop.search');

Route::get('/orders', 'Order@index')->name('orders');
Route::get('/orders/{id}', 'Order@show')->name('orders.show');

Route::get('/products', 'Product@index')->name('products');
Route::get('/products/edit/{id}', 'Product@edit')->name('products.edit');
Route::post('/products/update/{id}', 'Product@update')->name('products.update');
Route::get('/products/create', 'Product@create')->name('products.create');
Route::post('/products/store', 'Product@store')->name('products.store');
Route::get('/products/destroy/{id}', 'Product@destroy')->name('products.destroy');

//Route::get('/store', 'Store@index')->name('store');
Route::get('/store/create', 'Store@create')->name('store');
Route::post('/store/store', 'Store@store')->name('store.store');
Route::get('/store/search', 'Store@search')->name('store.search');

Route::get('/customers', 'Customer@index')->name('customers');
Route::get('/customers/edit/{id}', 'Customer@edit')->name('customers.edit');
Route::post('/customers/update/{id}', 'Customer@update')->name('customers.update');
Route::get('/customers/create', 'Customer@create')->name('customers.create');
Route::post('/customers/store', 'Customer@store')->name('customers.store');
Route::get('/customers/destroy/{id}', 'Customer@destroy')->name('customers.destroy');
Route::get('/customers/renew', 'Customer@points_renew')->name('customers.renew');

Route::get('/retails', 'Retail@index')->name('retails');
Route::get('/retails/edit/{id}', 'Retail@edit')->name('retails.edit');
Route::post('/retails/update/{id}', 'Retail@update')->name('retails.update');
Route::get('/retails/create', 'Retail@create')->name('retails.create');
Route::post('/retails/store', 'Retail@store')->name('retails.store');
Route::get('/retails/destroy/{id}', 'Retail@destroy')->name('retails.destroy');

Route::get('/users', 'User@index')->name('users');
Route::get('/users/edit/{id}', 'User@edit')->name('users.edit');
Route::post('/users/update/{id}', 'User@update')->name('users.update');
Route::get('/users/create', 'User@create')->name('users.create');
Route::post('/users/store', 'User@store')->name('users.store');
Route::get('/users/destroy/{id}', 'User@destroy')->name('users.destroy');

Route::get('/report', 'Report@index')->name('report');
