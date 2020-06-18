<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Mail\OrderMail;
use Mail;

class OrderController extends Controller
{
    public function index()
	{
	    //QUERY UNTUK MENGAMBIL SEMUA PESANAN DAN LOAD DATA YANG BERELASI MENGGUNAKAN EAGER LOADING
	    //DAN URUTANKAN BERDASARKAN CREATED_AT
	    $orders = Order::with(['customer.district.city.province'])
        ->withCount('return')
        ->orderBy('created_at', 'DESC');

	    //JIKA Q UNTUK PENCARIAN TIDAK KOSONG
	    if (request()->q != '') {
	        //MAKA DIBUAT QUERY UNTUK MENCARI DATA BERDASARKAN NAMA, INVOICE DAN ALAMAT
	        $orders = $orders->where(function($q) {
	            $q->where('customer_name', 'LIKE', '%' . request()->q . '%')
	            ->orWhere('invoice', 'LIKE', '%' . request()->q . '%')
	            ->orWhere('customer_address', 'LIKE', '%' . request()->q . '%');
	        });
	    }

	    //JIKA STATUS TIDAK KOSONG 
	    if (request()->status != '') {
	        //MAKA DATA DIFILTER BERDASARKAN STATUS
	        $orders = $orders->where('status', request()->status);
	    }
	    $orders = $orders->paginate(10); //LOAD DATA PER 10 DATA
	    return view('orders.index', compact('orders')); //LOAD VIEW INDEX DAN PASSING DATA TERSEBUT
	}

	public function destroy($id)
	{
	    $order = Order::find($id);
	    $order->details()->delete();
	    $order->payment()->delete();
	    $order->delete();
	    return redirect(route('orders.index'));
	}

	public function view($invoice)
	{
	    $order = Order::with(['customer.district.city.province', 'payment', 'details.product'])->where('invoice', $invoice)->first();
	    return view('orders.view', compact('order'));
	}

	public function acceptPayment($invoice)
	{
	    //MENGAMBIL DATA CUSTOMER BERDASARKAN INVOICE
	    $order = Order::with(['payment'])->where('invoice', $invoice)->first();
	    //UBAH STATUS DI TABLE PAYMENTS MELALUI ORDER YANG TERKAIT
	    $order->payment()->update(['status' => 1]);
	    //UBAH STATUS ORDER MENJADI PROSES
	    $order->update(['status' => 2]);
	    //REDIRECT KE HALAMAN YANG SAMA.
	    return redirect(route('orders.view', $order->invoice));
	}

	public function shippingOrder(Request $request)
	{
	    //MENGAMBIL DATA ORDER BERDASARKAN ID
	    $order = Order::with(['customer'])->find($request->order_id);
	    //UPDATE DATA ORDER DENGAN MEMASUKKAN NOMOR RESI DAN MENGUBAH STATUS MENJADI DIKIRIM
	    $order->update(['tracking_number' => $request->tracking_number, 'status' => 3]);
	    //KIRIM EMAIL KE PELANGGAN TERKAIT
	    Mail::to($order->customer->email)->send(new OrderMail($order));
	    //REDIRECT KEMBALI
	    return redirect()->back();
	}

	public function return($invoice)
	{
	    $order = Order::with(['return', 'customer'])->where('invoice', $invoice)->first();
	    return view('orders.return', compact('order'));
	}
}
