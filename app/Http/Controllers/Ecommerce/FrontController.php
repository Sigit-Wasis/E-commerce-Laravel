<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Province;
use App\Order;
use App\Customer;

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
	    return view('ecommerce.product', compact('products'));
	}

	public function categoryProduct($slug)
	{
	   //JADI QUERYNYA ADALAH KITA CARI DULU KATEGORI BERDASARKAN SLUG, SETELAH DATANYA DITEMUKAN
	   //MAKA SLUG AKAN MENGAMBIL DATA PRODUCT YANG BERELASI MENGGUNAKAN METHOD PRODUCT() YANG TELAH DIDEFINISIKAN PADA FILE CATEGORY.PHP SERTA DIURUTKAN BERDASARKAN CREATED_AT DAN DI-LOAD 12 DATA PER SEKALI LOAD
	    $products = Category::where('slug', $slug)->first()->product()->orderBy('created_at', 'DESC')->paginate(12);
	    //LOAD VIEW YANG SAMA YAKNI PRODUCT.BLADE.PHP KARENA TAMPILANNYA AKAN KITA BUAT SAMA JUGA
	    return view('ecommerce.product', compact('products'));
	}

	public function show($slug)
	{
	    //QUERY UNTUK MENGAMBIL SINGLE DATA BERDASARKAN SLUG-NYA
	    $product = Product::with(['category'])->where('slug', $slug)->first();
	    //LOAD VIEW SHOW.BLADE.PHP DAN PASSING DATA PRODUCT
	    return view('ecommerce.show', compact('product'));
	}

	public function verifyCustomerRegistration($token)
	{
	    //JADI KITA BUAT QUERY UNTUK MENGMABIL DATA USER BERDASARKAN TOKEN YANG DITERIMA
	    $customer = Customer::where('activate_token', $token)->first();
	    if ($customer) {
	        //JIKA ADA MAKA DATANYA DIUPDATE DENGNA MENGOSONGKAN TOKENNYA DAN STATUSNYA JADI AKTIF
	        $customer->update([
	            'activate_token' => null,
	            'status' => 1
	        ]);
	        //REDIRECT KE HALAMAN LOGIN DENGAN MENGIRIMKAN FLASH SESSION SUCCESS
	        return redirect(route('customer.login'))->with(['success' => 'Verifikasi Berhasil, Silahkan Login']);
	    }
	    //JIKA TIDAK ADA, MAKA REDIRECT KE HALAMAN LOGIN
	    //DENGAN MENGIRIMKAN FLASH SESSION ERROR
	    return redirect(route('customer.login'))->with(['error' => 'Invalid Verifikasi Token']);
	}

	public function customerSettingForm()
	{
	    //MENGAMBIL DATA CUSTOMER YANG SEDANG LOGIN
	    // Fungsi load() cara kerjanya sama dengan eager loading, maka definisikan relationships customer ke district.
	    $customer = auth()->guard('customer')->user()->load('district');
	    //GET DATA PROPINSI UNTUK DITAMPILKAN PADA SELECT BOX
	    $provinces = Province::orderBy('name', 'ASC')->get();
	    //LOAD VIEW setting.blade.php DAN PASSING DATA CUSTOMER - PROVINCES
	    return view('ecommerce.setting', compact('customer', 'provinces'));
	}

	public function customerUpdateProfile(Request $request)
	{
	    //VALIDASI DATA YANG DIKIRIM
	    $this->validate($request, [
	        'name' => 'required|string|max:100',
	        'phone_number' => 'required|max:15',
	        'address' => 'required|string',
	        'district_id' => 'required|exists:districts,id',
	        'password' => 'nullable|string|min:6'
	    ]);

	    //AMBIL DATA CUSTOMER YANG SEDANG LOGIN
	    $user = auth()->guard('customer')->user();
	    //AMBIL DATA YANG DIKIRIM DARI FORM
	    //TAPI HANYA 4 COLUMN SAJA SESUAI YANG ADA DI BAWAH
	    $data = $request->only('name', 'phone_number', 'address', 'district_id');
	    //ADAPUN PASSWORD KITA CEK DULU, JIKA TIDAK KOSONG
	    if ($request->password != '') {
	        //MAKA TAMBAHKAN KE DALAM ARRAY
	        $data['password'] = $request->password;
	    }
	    //TERUS UPDATE DATANYA
	    $user->update($data);
	    //DAN REDIRECT KEMBALI DENGAN MENGIRIMKAN PESAN BERHASIL
	    return redirect()->back()->with(['success' => 'Profil berhasil diperbaharui']);
	}

	public function referalProduct($user, $product)
	{
	    $code = $user . '-' . $product; //KITA MERGE USERID DAN PRODUCTID
	    $product = Product::find($product); //FIND PRODUCT BERDASARKAN PRODUCTID
	    $cookie = cookie('dw-afiliasi', json_encode($code), 2880); //BUAT COOKIE DENGAN NAMA DW-AFILIASI DAN VALUENYA ADALAH CODE YANG SUDAH DI-MERGE
	    //KEMUDIAN REDIRECT KE HALAMAN SHOW PRODUCT DAN MENGIRIMKAN COOKIE KE BROWSER
	    return redirect(route('front.show_product', $product->slug))->cookie($cookie);
	}

	public function listCommission()
	{
	    $user = auth()->guard('customer')->user(); //AMBIL DATA USER YANG LOGIN
	    //QUERY BERDASARKAN ID USER DARI DATA REF YANG ADA DIORDER DENGAN STATUS 4 ATAU SELESAI
	    $orders = Order::where('ref', $user->id)->where('status', 4)->paginate(10);
	    //LOAD VIEW AFFILIATE.BLADE.PHP DAN PASSING DATA ORDERS
	    return view('ecommerce.affiliate', compact('orders'));
	}
}