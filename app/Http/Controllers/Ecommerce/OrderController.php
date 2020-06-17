<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Payment;
use Carbon\Carbon;
use DB;
use PDF;
use App\OrderReturn;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
	{
	    $orders = Order::withCount(['return'])->where('customer_id', auth()->guard('customer')->user()->id)
        ->orderBy('created_at', 'DESC')->paginate(10);
    return view('ecommerce.orders.index', compact('orders'));
	}

	public function view($invoice)
	{
		// Line pertama adalah query untuk mengambil data order berdasarkan invoice, kemudian me-load data yang berelasi dengan menggunakan eager loading.
	    $order = Order::with(['district.city.province', 'details', 'details.product', 'payment'])
	        ->where('invoice', $invoice)->first();

	    //JADI KITA CEK, VALUE forUser() NYA ADALAH CUSTOMER YANG SEDANG LOGIN
	    //DAN ALLOW NYA MEMINTA DUA PARAMETER
	    //PERTAMA ADALAH NAMA GATE YANG DIBUAT SEBELUMNYA DAN YANG KEDUA ADALAH DATA ORDER DARI QUERY DI ATAS
	    if (\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
	        //JIKA HASILNYA TRUE, MAKA KITA TAMPILKAN DATANYA
	        return view('ecommerce.orders.view', compact('order'));
	    }
	    //JIKA FALSE, MAKA REDIRECT KE HALAMAN YANG DIINGINKAN
	    return redirect(route('customer.orders'))->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Order Orang Lain']);
	}

	public function paymentForm()
	{
	    return view('ecommerce.payment');
	}

	public function storePayment(Request $request)
	{
	  //VALIDASI DATANYA
	    $this->validate($request, [
	        'invoice' => 'required|exists:orders,invoice',
	        'name' => 'required|string',
	        'transfer_to' => 'required|string',
	        'transfer_date' => 'required',
	        'amount' => 'required|integer',
	        'proof' => 'required|image|mimes:jpg,png,jpeg'
	    ]);

	    //DEFINE DATABASE TRANSACTION UNTUK MENGHINDARI KESALAHAN SINKRONISASI DATA JIKA TERJADI ERROR DITENGAH PROSES QUERY
	    DB::beginTransaction();
	    try {
	        //AMBIL DATA ORDER BERDASARKAN INVOICE ID
	        $order = Order::where('invoice', $request->invoice)->first();
	        if ($order->subtotal != $request->amount) return redirect()->back()->with(['error' => 'Error, Pembayaran Harus Sama Dengan Tagihan']); 
	        //JIKA STATUSNYA MASIH 0 DAN ADA FILE BUKTI TRANSFER YANG DI KIRIM
	        if ($order->status == 0 && $request->hasFile('proof')) {
	            //MAKA UPLOAD FILE GAMBAR TERSEBUT
	            $file = $request->file('proof');
	            $filename = time() . '.' . $file->getClientOriginalExtension();
	            $file->storeAs('public/payment', $filename);

	            //KEMUDIAN SIMPAN INFORMASI PEMBAYARANNYA
	            Payment::create([
	                'order_id' => $order->id,
	                'name' => $request->name,
	                'transfer_to' => $request->transfer_to,
	                'transfer_date' => Carbon::parse($request->transfer_date)->format('Y-m-d'),
	                'amount' => $request->amount,
	                'proof' => $filename,
	                'status' => false
	            ]);
	            //DAN GANTI STATUS ORDER MENJADI 1
	            $order->update(['status' => 1]);
	            //JIKA TIDAK ADA ERROR, MAKA COMMIT UNTUK MENANDAKAN BAHWA TRANSAKSI BERHASIL
	            DB::commit();
	            //REDIRECT DAN KIRIMKAN PESAN
	            return redirect()->back()->with(['success' => 'Pesanan Dikonfirmasi']);
	        }
	        //REDIRECT DENGAN ERROR MESSAGE
	        return redirect()->back()->with(['error' => 'Error, Upload Bukti Transfer']);
	    } catch(\Exception $e) {
	        //JIKA TERJADI ERROR, MAKA ROLLBACK SELURUH PROSES QUERY
	        DB::rollback();
	        //DAN KIRIMKAN PESAN ERROR
	        return redirect()->back()->with(['error' => $e->getMessage()]);
	    }
	}
	
	public function pdf($invoice)
	{
	    //GET DATA ORDER BERDASRKAN INVOICE
	    $order = Order::with(['district.city.province', 'details', 'details.product', 'payment'])
	        ->where('invoice', $invoice)->first();
	    //MENCEGAH DIRECT AKSES OLEH USER, SEHINGGA HANYA PEMILIKINYA YANG BISA MELIHAT FAKTURNYA
	    if (!\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
	        return redirect(route('customer.view_order', $order->invoice));
	    }

	    //JIKA DIA ADALAH PEMILIKNYA, MAKA LOAD VIEW BERIKUT DAN PASSING DATA ORDERS
	    $pdf = PDF::loadView('ecommerce.orders.pdf', compact('order'));
	    //KEMUDIAN BUKA FILE PDFNYA DI BROWSER
	    return $pdf->stream();
	}

	public function acceptOrder(Request $request)
	{
	    //CARI DATA ORDER BERDASARKAN ID
	    $order = Order::find($request->order_id);
	    //VALIDASI KEPEMILIKAN
	    if (!\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
	        return redirect()->back()->with(['error' => 'Bukan Pesanan Kamu']);
	    }

	    //UBAH STATUSNYA MENJADI 4
	    $order->update(['status' => 4]);
	    //REDIRECT KEMBALI DENGAN MENAMPILKAN ALERT SUCCESS
	    return redirect()->back()->with(['success' => 'Pesanan Dikonfirmasi']);
	}

	public function returnForm($invoice)
	{
	    //LOAD DATA BERDASARKAN INVOICE
	    $order = Order::where('invoice', $invoice)->first();
	    //LOAD VIEW RETURN.BLADE.PHP DAN PASSING DATA ORDER
	    return view('ecommerce.orders.return', compact('order'));
	}

	public function processReturn(Request $request, $id)
	{
	    //LAKUKAN VALIDASI DATA
	    $this->validate($request, [
	        'reason' => 'required|string',
	        'refund_transfer' => 'required|string',
	        'photo' => 'required|image|mimes:jpg,png,jpeg'
	    ]);

	    //CARI DATA RETURN BERDASARKAN order_id YANG ADA DITABLE ORDER_RETURNS NANTINYA
	    $return = OrderReturn::where('order_id', $id)->first();
	    //JIKA DITEMUKAN, MAKA TAMPILKAN NOTIFIKASI ERROR
	    if ($return) return redirect()->back()->with(['error' => 'Permintaan Refund Dalam Proses']);

	    //JIKA TIDAK, LAKUKAN PENGECEKAN UNTUK MEMASTIKAN FILE FOTO DIKIRIMKAN
	    if ($request->hasFile('photo')) {
	        //GET FILE
	        $file = $request->file('photo');
	        //GENERATE NAMA FILE BERDASARKAN TIME DAN STRING RANDOM
	        $filename = time() . Str::random(5) . '.' . $file->getClientOriginalExtension();
	        //KEMUDIAN UPLOAD KE DALAM FOLDER STORAGE/APP/PUBLIC/RETURN
	        $file->storeAs('public/return', $filename);

	        //DAN SIMPAN INFORMASINYA KE DALAM TABLE ORDER_RETURNS
	        OrderReturn::create([
	            'order_id' => $id,
	            'photo' => $filename,
	            'reason' => $request->reason,
	            'refund_transfer' => $request->refund_transfer,
	            'status' => 0
	        ]);
	        //LALU TAMPILKAN NOTIFIKASI SUKSES
	        return redirect()->back()->with(['success' => 'Permintaan Refund Dikirim']);
	    }
	}
}
