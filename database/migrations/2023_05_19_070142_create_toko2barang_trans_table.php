<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToko2barangTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('toko2barang_trans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trans_id')->constrained('toko2_trans')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('toko_barangs')->onDelete('cascade');
            $table->integer('jumlah');
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
        Schema::dropIfExists('toko2barang_trans');
    }
}
