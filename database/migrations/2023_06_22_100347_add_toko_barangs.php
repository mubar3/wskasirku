<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokoBarangs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('toko_barangs', function (Blueprint $table) {
            $table->enum('is_produk',['y','n'])->default('y')->after('tak_terbatas');
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('toko_barangs', function (Blueprint $table) {
            $table->dropColumn('is_produk');
        });
        //
    }
}
