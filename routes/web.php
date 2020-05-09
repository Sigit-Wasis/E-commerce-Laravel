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

// JADI INI GROUPING ROUTE, SEHINGGA SEMUA ROUTE YANG ADA DIDALAMNYA
// SECARA OTOMATIS AKAN DIAWALI DENGAN administrator
// CONTOH ; administrator/category/ ATAU /administrator/product dan SEBAGAINYA
Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function() {
	// ROUTING INI SUDAH ADA PADA SAAT PEMBUATAN AUTHENTICATION LARAVEL
	Route::get('/home', 'HomeController@index')->name('home');
	// ROUTING UNTUK CATEGORY
	Route::resource('category', 'CategoryController')->except(['create', 'show']);

	Route::resource('product', 'ProductController');

	Route::resource('product', 'ProductController')->except(['show']); //BAGIAN INI KITA TAMBAHKAN EXCETP KARENA METHOD SHOW TIDAK DIGUNAKAN
	Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.bulk'); //TAMBAHKAN ROUTE INI

	Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');
});

