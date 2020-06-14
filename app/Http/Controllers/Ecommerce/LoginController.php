<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function loginForm()
    {
        if (auth()->guard('customer')->check()) return redirect(route('customer.dashboard'));
	    return view('ecommerce.login');
    }

    public function login(Request $request)
	{
	    //VALIDASI DATA YANG DITERIMA
	    $this->validate($request, [
	        'email' => 'required|email|exists:customers,email',
	        'password' => 'required|string'
	    ]);

	    //CUKUP MENGAMBIL EMAIL DAN PASSWORD SAJA DARI REQUEST
	    //KARENA JUGA DISERTAKAN TOKEN
	    $auth = $request->only('email', 'password');
	    $auth['status'] = 1; //TAMBAHKAN JUGA STATUS YANG BISA LOGIN HARUS 1
	  
	    //CHECK UNTUK PROSES OTENTIKASI
	    //DARI GUARD CUSTOMER, KITA ATTEMPT PROSESNYA DARI DATA $AUTH
	    if (auth()->guard('customer')->attempt($auth)) {
	        //JIKA BERHASIL MAKA AKAN DIREDIRECT KE DASHBOARD
	        return redirect()->intended(route('customer.dashboard'));
	    }
	    //JIKA GAGAL MAKA REDIRECT KEMBALI BERSERTA NOTIFIKASI
	    return redirect()->back()->with(['error' => 'Email / Password Salah']);
	}

	public function dashboard()
	{
	    return view('ecommerce.dashboard');
	}

	public function logout()
	{
	    auth()->guard('customer')->logout(); //JADI KITA LOGOUT SESSION DARI GUARD CUSTOMER
	    return redirect(route('customer.login'));
	}
}