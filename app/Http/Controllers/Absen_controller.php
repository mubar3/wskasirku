<?php

namespace App\Http\Controllers;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Toko_tran;
use App\Models\Toko2_tran;
use App\Models\Toko2barang_tran;
use App\Models\Toko_barang;
use App\Models\Absen;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Absen_controller extends Controller
{
    public function add_absensi(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'tanggal' => 'required',
            'status' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        
        Absen::create([
            'userid' => $user->id,
            'tanggal' => $data->tanggal,
            'status' => $data->status,
        ]);
        return response()->json(['status'=>true,'message'=>'Berhasil']);
        
    }

    public function get_absen(Request $data)
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
        
        $data_absen=Absen::where('userid',$user->id)
            ->whereBetween('tanggal', [$data->tanggal_awal . ' 00:00:00', $data->tanggal_akhir . ' 23:59:59'])
            ->get();
        return response()->json(['status'=>true,'data'=>$data_absen]);
    }
}
