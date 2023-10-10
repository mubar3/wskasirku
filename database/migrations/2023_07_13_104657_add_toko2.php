<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToko2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tokos', function (Blueprint $table) {
            $table->time('jam_masuk')->after('gaji_harian');
            $table->text('jam_kerja')->after('jam_masuk');
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
        Schema::table('tokos', function (Blueprint $table) {
            $table->dropColumn('jam_masuk');
            $table->dropColumn('jam_kerja');
        });
        //
    }
}
