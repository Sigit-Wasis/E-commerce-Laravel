<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldStatusToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //KITA PILIH DATABASE YANG AKAN DITAMBAHKAN FIELD BARU
        Schema::table('products', function (Blueprint $table) {
            //KEMUDIAN TAMBAHKAN FIELDNYA DENGAN TIPE BOOLEAN DAN DISIMPAN SETELAH WEIGHT
            $table->boolean('status')->default(true)->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //APABILA ROLLBACK ATAU REFRESH DIJALANKAN
        Schema::table('products', function (Blueprint $table) {
            //MAKA AKAN MENGHAPUS FIELD STATUS DARI TABLE PRODUCTS
            $table->dropColumn('status');
        });
    }
}
