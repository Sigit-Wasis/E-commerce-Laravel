<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // BUAT TABLE cities
        Schema::create('cities', function (Blueprint $table) {
            // DENGAN FIELD DIBAWAH INI
            $table->bigIncrements('id');
            $table->unsignedBigInteger('province_id'); // FIELD INI AKAN MERUJUK KE TABLE provinces
            $table->string('name');
            $table->string('type');
            $table->string('postal_code');
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
        Schema::dropIfExists('cities');
    }
}
