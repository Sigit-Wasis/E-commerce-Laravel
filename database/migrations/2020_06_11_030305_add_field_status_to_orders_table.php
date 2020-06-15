<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldStatusToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Pada method up(), kita menambahkan field status dengan type char dan dilimit 1, nilai default-nya adalah 0 dan dilengkapi dengan command column untuk menjelaskan masing-masing code status-nya, kemudian column tersebut diletakkan setelah column subtotal.
        Schema::table('orders', function (Blueprint $table) {
            $table->char('status', 1)->default(0)->comment('0: new, 1: confirm, 2: process, 3: shipping, 4: done')->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
