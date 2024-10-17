<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbsens2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('absens', function (Blueprint $table) {
            $table->text('foto2')->after('foto')->nullable();
            $table->time('jam_masuk')->after('foto2')->nullable();
            $table->text('jam_kerja')->after('jam_masuk')->nullable();
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
        Schema::table('absens', function (Blueprint $table) {
            $table->dropColumn('foto2');
            $table->dropColumn('jam_masuk');
            $table->dropColumn('jam_kerja');
        });
        //
    }
}
