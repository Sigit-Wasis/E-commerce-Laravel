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

Route::get('/', 'Ecommerce\FrontController@index')->name('front.index');

// Product
Route::get('/product', 'Ecommerce\FrontController@product')->name('front.product');
Route::get('/product/{slug}', 'Ecommerce\FrontController@show')->name('front.show_product');

// Category
Route::get('/category/{slug}', 'Ecommerce\FrontController@categoryProduct')->name('front.category');

// Cart
Route::post('cart', 'Ecommerce\CartController@addToCart')->name('front.cart');
Route::get('/cart', 'Ecommerce\CartController@listCart')->name('front.list_cart');
Route::post('/cart/update', 'Ecommerce\CartController@updateCart')->name('front.update_cart');

// Cart-Checkout
Route::get('/checkout', 'Ecommerce\CartController@checkout')->name('front.checkout');
Route::post('/checkout', 'Ecommerce\CartController@processCheckout')->name('front.store_checkout');
Route::get('/checkout/{invoice}', 'Ecommerce\CartController@checkoutFinish')->name('front.finish_checkout');

Route::group(['prefix' => 'member', 'namespace' => 'Ecommerce'], function() {
	Route::get('login', 'LoginController@loginForm')->name('customer.login'); //TAMBAHKAN ROUTE INI
    Route::get('verify/{token}', 'FrontController@verifyCustomerRegistration')->name('customer.verify');
    Route::post('login', 'LoginController@login')->name('customer.post_login');

    Route::group(['middleware' => 'customer'], function() {
    	Route::get('dashboard', 'LoginController@dashboard')->name('customer.dashboard');
    	Route::get('orders', 'OrderController@index')->name('customer.orders');
    	Route::get('orders/{invoice}', 'OrderController@view')->name('customer.view_order');
    	
    	Route::get('logout', 'LoginController@logout')->name('customer.logout');
	});
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
	// Route::resource('product', 'ProductController');
	Route::resource('product', 'ProductController')->except(['show']); //BAGIAN INI KITA TAMBAHKAN EXCETP KARENA METHOD SHOW TIDAK DIGUNAKAN
	Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.bulk'); //TAMBAHKAN ROUTE INI
	Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');
});

