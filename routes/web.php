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

Route::get('/pay', function () {
    return view('split');
});


Route::get('/status', function () {
    return view('bvn_status');
});

Route::get('/failed', function () {
    return view('failed');
});



Route::get('/home', 'HomeController@index')->name('home');

Route::post('/verify', 'raveController@index')->name('verify');

Route::post('/split', 'raveController@initialize')->name('split');

Route::post('/rave/callback', 'RaveController@callback')->name('callback');

Route::post('/payment/process', 'PaymentController@pay');

Route::POST('/payment/process/update/{transaction_reference}', 'PaymentController@updateTransaction');

Auth::routes();
