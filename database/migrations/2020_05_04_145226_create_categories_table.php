<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // METHOD INI DIGUNAKAN KETIKA MIGRATE DI JALANKAN
    public function up()
    {
        // JADI KETIKA MIGRATE DIJALANKAN, KITA AKAN MEMBUAT TABLE BERNAMA categories
        Schema::create('categories', function (Blueprint $table) {
            // ADAPUN FIELDNYA ADALAH MASING-MASING DIBAWAH INI
            $table->bigIncrements('id');
            $table->string('name'); // STRING SAMA DENGAN VARCHAR

            /*
            JADI KITA AKAN MEMBUAT CATEGORI INI MEMILIKI ANAK KATEOGIR
            SEHINGGA DIBUAT STRUKTUR DIMANA KATEGORI YANG MEMILIKI parent_id
            ADALAH KATEGORI ANAK SEBALIKNYA JIKA KATEGORI ITU parent_id NYA NULL
            MAKA DIA ADALAH KATEGORI INDUK
            */
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // SEDANGKAN METHOD INI DIGUNAKAN KETIKA ROOLBACK ATAU REFRESH DATABASE DIJALANKAN
    public function down()
    {
        Schema::dropIfExists('categories'); // BAGIAN INI KITA AKAN MENGHAPUS DATABASE DIATAS
        // INGAT FUNGSI INI HANYA AKAN BERJALAN KETIKA KITA MENJALANKAN FUNGSI ROLLBACK ATAU YANG SEJENISNYA.
    }
}
