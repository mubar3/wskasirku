<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Toko_barang;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $toko=DB::table('tokos')->insertGetId([
            'nama' => 'cerita kita',
            'created_at'    =>Carbon::now(),
        ]);

        User::insert([
            [
                'name' => 'user_tester',
                'email' => 'user_tester@user_tester.com',
                'password' => '82242021f9eaf3859186ef257b0503c9c18bc1ca7d344b9cc739ec0762ca6906', //asd
                'key'       =>'268',
                'jenis'       =>'karyawan',
                'toko_id'       =>$toko,
                'created_at'    =>Carbon::now(),
            ],
            [
                'name' => 'admin_tester',
                'email' => 'admin_tester@admin_tester.com',
                'password' => '82242021f9eaf3859186ef257b0503c9c18bc1ca7d344b9cc739ec0762ca6906', //asd
                'key'       =>'268',
                'jenis'       =>'utama',
                'toko_id'       =>$toko,
                'created_at'    =>Carbon::now(),
            ]
        ]);

        Toko_barang::insert([
            [
                'nama'          => 'cheese taro',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'cheese greentea',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'cheese redvalvet',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'choco coklat',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'choco redvelved',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'choco greentea',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'choco taro',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'choco tiramisu',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'choco black forez',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'choco oreo',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Dalgona oreo',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Dalgona Black forez',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Boba Black forez',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Boba redvelved',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Boba Taro',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Boba greentea',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Boba oreo',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Boba coklat',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Boba tiramisu',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Mix Redvelved taro',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Mix Greentea  redvelved',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Spesial Greentea',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Spesial Redvalvet',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Spesial Taro',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Redvalver x Taro',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Greentea x Redvalvet',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'greentea x taro',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'z pencairan grab',
                'harga'         => '0',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'z pencairan gojek',
                'harga'         => '0',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'z pencairan shopeefood',
                'harga'         => '0',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'z kebutuhan',
                'harga'         => '0',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'z isi galon',
                'harga'         => '-4000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'z es batu',
                'harga'         => '-6000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ],
            [
                'nama'          => 'Boba greentea',
                'harga'         => '8000',
                'created_at'    =>Carbon::now(),
                'toko_id'       =>$toko,
            ]
        ]);


    }
}
