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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('/claims', 'ClaimController');

Route::get('/import/users', 'ImportController@importUserDealers');
Route::get('/import/orders', 'ImportController@importOrders');
Route::get('/orders/accepted','HomeController@getAccepted');
Route::get('/orders/rejected','HomeController@getRejected');
