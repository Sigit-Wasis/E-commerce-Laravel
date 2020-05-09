<?php

namespace App\Http\Controllers;
use App\Category; //LOAD MODEL CATEGORY

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        //BUAT QUERY KE DATABASE MENGGUNAKAN MODEL CATEGORY DENGAN MENGURUTKAN BERDASARKAN CREATED_AT DAN DISET DESCENDING, KEMUDIAN PAGINATE(10) BERARTI HANYA ME-LOAD 10 DATA PER PAGENYA
        //YANG MENARIK ADALAH FUNGSI WITH(), DIMANA FUNGSI INI DISEBUT EAGER LOADING
        //ADAPUN NAMA YANG DISEBUTKAN DIDALAMNYA ADALAH NAMA METHOD YANG DIDEFINISIKAN DIDALAM MODEL CATEGORY
        //METHOD TERSEBUT BERISI FUNGSI RELATIONSHIPS ANTAR TABLE
        //JIKA LEBIH DARI 1 MAKA DAPAT DIPISAHKAN DENGAN KOMA, 
        // CONTOH: with(['parent', 'contoh1', 'contoh2'])
        $category = Category::with(['parent'])->orderBy('created_at', 'DESC')->paginate(10);
      
        //QUERY INI MENGAMBIL SEMUA LIST CATEGORY DARI TABLE CATEGORIES, PERHATIKAN AKHIRANNYA ADALAH GET() TANPA ADA LIMIT
        //LALU getParent() DARI MANA? METHOD TERSEBUT ADALAH SEBUAH LOCAL SCOPE
        //Local scope digunakan untuk mengelompokkan query yang bisa digunakan kembali pada kondisi lain sehingga kita tidak perlu menuliskan kembali query tersebut.
        $parent = Category::getParent()->orderBy('name', 'ASC')->get();
      
        //LOAD VIEW DARI FOLDER CATEGORIES, DAN DIDALAMNYA ADA FILE INDEX.BLADE.PHP
        //KEMUDIAN PASSING DATA DARI VARIABLE $category & $parent KE VIEW AGAR DAPAT DIGUNAKAN PADA VIEW TERKAIT
        return view('categories.index', compact('category', 'parent'));
    }

    public function store(Request $request)
	{
	    //JADI KITA VALIDASI DATA YANG DITERIMA, DIMANA NAME CATEGORY WAJIB DIISI
	    //TIPENYA ADA STRING DAN MAX KARATERNYA ADALAH 50 DAN BERSIFAT UNIK
	    //UNIK MAKSUDNYA JIKA DATA DENGAN NAMA YANG SAMA SUDAH ADA MAKA VALIDASINYA AKAN MENGEMBALIKAN ERROR
	    $this->validate($request, [
	        'name' => 'required|string|max:50|unique:categories'
	    ]);

	    //FIELD slug AKAN DITAMBAHKAN KEDALAM COLLECTION $REQUEST
	    $request->request->add(['slug' => $request->name]);
	  
	    //SEHINGGA PADA BAGIAN INI KITA TINGGAL MENGGUNAKAN $request->except()
	    //YAKNI MENGGUNAKAN SEMUA DATA YANG ADA DIDALAM $REQUEST KECUALI INDEX _TOKEN
	    //FUNGSI REQUEST INI SECARA OTOMATIS AKAN MENJADI ARRAY
	    //CATEGORY::CREATE ADALAH MASS ASSIGNMENT UNTUK MEMBERIKAN INSTRUKSI KE MODEL AGAR MENAMBAHKAN DATA KE TABLE TERKAIT
	    Category::create($request->except('_token'));
	    //APABILA BERHASIL, MAKA REDIRECT KE HALAMAN LIST KATEGORI
	    //DAN MEMBUAT FLASH SESSION MENGGUNAKAN WITH()
	    //JADI WITH() DISINI BERBEDA FUNGSINYA DENGAN WITH() YANG DISAMBUNGKAN DENGAN MODEL
	    return redirect(route('category.index'))->with(['success' => 'Kategori Baru Ditambahkan!']);
	}

	public function edit($id)
	{
	    $category = Category::find($id); //QUERY MENGAMBIL DATA BERDASARKAN ID
	    $parent = Category::getParent()->orderBy('name', 'ASC')->get(); //INI SAMA DENGAN QUERY YANG ADA PADA METHOD INDEX
	  
	    //LOAD VIEW EDIT.BLADE.PHP PADA FOLDER CATEGORIES
	    //DAN PASSING VARIABLE CATEGORY & PARENT
	    return view('categories.edit', compact('category', 'parent'));
	}

	public function update(Request $request, $id)
	{
	    //VALIDASI FIELD NAME
	    //YANG BERBEDA ADA TAMBAHAN PADA RULE UNIQUE
	    //FORMATNYA ADALAH unique:nama_table,nama_field,id_ignore
	    //JADI KITA TETAP MENGECEK UNTUK MEMASTIKAN BAHWA NAMA CATEGORYNYA UNIK
	    //AKAN TETAPI KHUSUS DATA DENGAN ID YANG AKAN DIUPDATE DATANYA DIKECUALIKAN
	    $this->validate($request, [
	        'name' => 'required|string|max:50|unique:categories,name,' . $id
	    ]);

	    $category = Category::find($id); //QUERY UNTUK MENGAMBIL DATA BERDASARKAN ID
	    //KEMUDMIAN PERBAHARUI DATANYA
	    //POSISI KIRI ADALAH NAMA FIELD YANG ADA DITABLE CATEGORIES
	    //POSISI KANAN ADALAH VALUE DARI FORM EDIT
	    $category->update([
	        'name' => $request->name,
	        'parent_id' => $request->parent_id
	    ]);
	  
	    //REDIRECT KE HALAMAN LIST KATEGORI
	    return redirect(route('category.index'))->with(['success' => 'Kategori Diperbaharui!']);
	}

	public function destroy($id)
	{
	    //TAMBAHKAN product KEDALAM ARRAY WITHCOUNT()
	    //FUNGSI INI AKAN MEMBENTUK FIELD BARU YANG BERNAMA product_count
	    $category = Category::withCount(['child', 'product'])->find($id);
	    //KEMUDIAN PADA IF STATEMENTNYA KITA CEK JUGA JIKA = 0
	    if ($category->child_count == 0 && $category->product_count == 0) {
	        $category->delete();
	        return redirect(route('category.index'))->with(['success' => 'Kategori Dihapus!']);
	    }
	    return redirect(route('category.index'))->with(['error' => 'Kategori Ini Memiliki Anak Kategori!']);
	}
}
