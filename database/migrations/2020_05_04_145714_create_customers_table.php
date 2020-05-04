<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // BUAT TABLE customers
        Schema::create('customers', function (Blueprint $table) {
            // DENGAN FIELD SEBAGAI BERIKUT
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique(); // FIELD INI DIBUAT UNTUK MENGHINDARI DUPLIKAT DATA
            $table->string('phone_number');
            $table->string('address');
            $table->unsignedBigInteger('district_id'); // FIELD INI AKAN MERUJUK PADA TABLE districts NANTINYA UNTUK MENGAMBIL DATA KOTA CUTOMER
            $table->boolean('status')->default(false); // TIPENYA BOOLEAN, DENGAN NILAI DEFAULT ADALAH FALSE
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
        Schema::dropIfExists('customers');
    }
}
