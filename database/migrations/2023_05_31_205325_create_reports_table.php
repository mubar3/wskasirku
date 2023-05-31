<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('toko_id')->constrained('tokos')->onDelete('cascade');
            $table->date('tgl_awal');
            $table->date('tgl_akhir');
            $table->text('status');
            $table->text('keuntungan_bersih')->nullable();
            $table->text('keuntungan_kotor')->nullable();
            $table->text('total_penjualan')->nullable();
            $table->text('restok')->nullable();
            $table->text('gaji_sewa')->nullable();
            $table->text('total_gaji')->nullable();
            $table->text('biaya_sewa')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'tgl_awal', 'tgl_akhir']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
