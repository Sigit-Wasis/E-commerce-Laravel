<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // MEMBUAT TABLE order_datails
        Schema::create('order_details', function (Blueprint $table) {
            // DENGAN FIELD DIBAWAH INI
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id'); // FIELD INI AKAN MERUJUK KE TABLE orders
            $table->unsignedBigInteger('product_id'); // FIELD INI AKAN MERUJUK KE TABLE products
            $table->integer('price'); // TIDAK SAMA DENGAN CUSTOMER, INFORMASI HARGA SAAT BARANG INI DIPESAN JUGA DIBUAT SALINANNYA
            $table->integer('qty');
            $table->integer('weight'); // JUGA BERLAKU DENGAN BERAT BARANG, UNTUK MENGHINDARI PERUBAHAN DATA PRODUK
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
