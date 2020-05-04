<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // JADI KITA AKAN MEMBUAT TABLE products
        Schema::create('products', function (Blueprint $table) {
            // DENGAN FIELD DIBAWAH INI
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');

            // ADAPUN category_id NYA NANTI AKAN MERUJUK KE TABLE categories
            // DIMANA UNTUK SAAT INI BELUM AKAN DIBAHAS RELASI ANTAR TABLENYA
            $table->unsignedBigInteger('category_id');
            $table->text('description')->nullable();
            $table->string('image');
            $table->integer('price');
            $table->integer('weight');
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
        Schema::dropIfExists('products');
    }
}