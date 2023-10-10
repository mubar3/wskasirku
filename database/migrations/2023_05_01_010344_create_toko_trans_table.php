<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokoTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('toko_trans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('toko_barangs')->onDelete('cascade');
            $table->enum('jenis',['tambah','kurang'])->nullable()->default('tambah');
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
        Schema::dropIfExists('toko_trans');
    }
}
