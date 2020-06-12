<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Category;

class FrontController extends Controller
{
    public function index()
	{
		//MEMBUAT QUERY UNTUK MENGAMBIL DATA PRODUK YANG DIURUTKAN BERDASARKAN TGL TERBARU
		//DAN DI-LOAD 10 DATA PER PAGENYA
		$products = Product::orderBy('created_at', 'DESC')->paginate(10);
		//LOAD VIEW INDEX.BLADE.PHP DAN PASSING DATA DARI VARIABLE PRODUCTS
		return view('ecommerce.index', compact('products'));
	}

	public function product()
	{
	    //BUAT QUERY UNTUK MENGAMBIL DATA PRODUK, LOAD PER PAGENYA KITA GUNAKAN 12 AGAR PRESISI PADA HALAMAN TERSEBUT KARENA DALAM SEBARIS MEMUAT 4 BUAH PRODUK
	    $products = Product::orderBy('created_at', 'DESC')->paginate(12);
	    //LOAD VIEW PRODUCT.BLADE.PHP DAN PASSING KEDUA DATA DIATAS
	    return view('ecommerce.product', compact('products', 'categories'));
	}
}