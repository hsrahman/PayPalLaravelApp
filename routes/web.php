<?php

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

Route::get('/', "PagesController@index");
Route::get('/checkout', "CheckoutController@checkout");
Route::post('/addItem/{id}', "PagesController@addItem")->name("addItem");
Route::post('/removeItem/{id}', "CheckoutController@removeItem")->name("removeItem");;
Route::get('/paypalRedirect', "CheckoutController@paypalRedirect");
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
