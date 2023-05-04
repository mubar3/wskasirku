<?php

namespace App\Http\Controllers;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Toko_tran;
use App\Models\Toko_barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Trans_controller extends Controller
{
    public function get_barang(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        $barang=Toko_barang::select(
            '*',
            DB::raw("CASE WHEN foto = '' THEN CONCAT('".url('/storage/barang')."','/',foto) ELSE '' END AS foto")
            )
            ->where('toko_id',$user->toko_id)
            ->where('status','y')
            ->get();

        if(!empty($data->tanggal_awal)){
            foreach ($barang as $key) {
                $key->tambah=Toko_tran::whereBetween('created_at',[$data->tanggal_awal,Carbon::parse($data->tanggal_akhir)->addDay()])
                        ->where('barang_id',$key->id)
                        ->where('jenis','tambah')
                        ->count();
                $key->kurang=Toko_tran::whereBetween('created_at',[$data->tanggal_awal,Carbon::parse($data->tanggal_akhir)->addDay()])
                        ->where('barang_id',$key->id)
                        ->where('jenis','kurang')
                        ->count();
            }
        }
        return response()->json(['status'=>true,'message'=>'sukses','data'=>$barang]);
    }

    public function add_stok(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'barang_id' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        
        DB::beginTransaction();
        try {
            Toko_tran::create([
                'barang_id' => $data->barang_id, 
                'jenis' => 'tambah', 
            ]);
            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }
    
    public function remove_stok(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'barang_id' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        
        DB::beginTransaction();
        try {
            Toko_tran::create([
                'barang_id' => $data->barang_id, 
                'jenis' => 'kurang', 
            ]);
            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }

    public function detail_penjualan(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'tanggal_awal' => 'required',
            'tanggal_akhir' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        $data->tanggal_akhir=Carbon::parse($data->tanggal_akhir)->addDay();
        $penjualan_tambah=Toko_tran::whereBetween('created_at',[$data->tanggal_awal,$data->tanggal_akhir])
            ->where('jenis','tambah')
            ->get();

        $penjualan_kurang=Toko_tran::whereBetween('created_at',[$data->tanggal_awal,$data->tanggal_akhir])
            ->where('jenis','kurang')
            ->get();

        return response()->json([
            'status'=>true,
            'message'=>'Berhasil',
            'tambah'=>count($penjualan_tambah),
            'kurang'=>count($penjualan_kurang),
            'total'=>count($penjualan_tambah)-count($penjualan_kurang),
            'detail'=>Toko_tran::select(
                    'toko_trans.*',
                    'toko_barangs.nama as nama_barang',
                )
                ->whereBetween('toko_trans.created_at',[$data->tanggal_awal,$data->tanggal_akhir])
                ->join('toko_barangs','toko_barangs.id','=','toko_trans.barang_id')
                ->get(),
        ]);
    }
}
