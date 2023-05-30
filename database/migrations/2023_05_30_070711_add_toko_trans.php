<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokoTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('toko2_trans', function (Blueprint $table) {
            $table->foreignId('userid')->nullable()->after('toko_id')->constrained('users')->onDelete('cascade');
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
        Schema::table('toko2_trans', function (Blueprint $table) {
            $table->dropForeign('toko2_trans_userid_foreign');
            $table->dropColumn('userid');
        });
        //
    }
}
