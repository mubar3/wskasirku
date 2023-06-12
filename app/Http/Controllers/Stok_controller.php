<?php

namespace App\Http\Controllers;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Toko_tran;
use App\Models\Toko2_tran;
use App\Models\Toko2barang_tran;
use App\Models\Toko_barang;
use App\Models\Bahan;
use App\Models\Stok_bahan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Stok_controller extends Controller
{
    public function update_bahan(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'bahan_id' => 'required',
            'banyak' => 'required',
            'nama' => 'required',
            // 'ket' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::join('tokos','tokos.id','=','users.toko_id')
            ->where('users.session',$data->session)
            ->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        DB::beginTransaction();
        try {

            $bahan=Bahan::find($data->bahan_id);
            $total=(int)$bahan->stok_gr + (int)$data->banyak;
            $bahan->update([
                'stok_gr' => $total,
                'nama' => $data->nama,
                'ket' => $data->ket,
            ]);

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }

    public function get_bahan(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::join('tokos','tokos.id','=','users.toko_id')
            ->where('users.session',$data->session)
            ->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        DB::beginTransaction();
        try {
            $bahan=Bahan::where('toko_id',$user->toko_id)->get();

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil','data'=>$bahan]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }

    public function add_bahan(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'nama' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::join('tokos','tokos.id','=','users.toko_id')
            ->where('users.session',$data->session)
            ->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        DB::beginTransaction();
        try {
            Bahan::create([
                    'nama' => $data->nama,
                    'stok_gr' => 0,
                    'toko_id' => $user->toko_id,
                ]);

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }
}
