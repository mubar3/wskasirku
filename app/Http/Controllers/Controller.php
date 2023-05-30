<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absen;
use Session;
use Validator;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\Trans_controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function get_session()
    {
        $code=Session::getId();
        if (User::where('session', $code)->exists()) {
            $this->get_session();
        }
        return $code;   
    }
    public function encryptHash($pass, $key)
    {
        $hashPass = hash('sha256', $pass);
        $encryptPass = hash('sha256', $key . $hashPass);
        return $encryptPass;
    }

    public function report(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'tanggal_awal' => 'required',
            'tanggal_akhir' => 'required',
            'keuntungan' => 'required',
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
        $response=json_decode(json_encode((new Trans_controller)->get_transaksi($data)));
        $total_penjualan=0;
        foreach ($response->original->data as $key) {
            $total_penjualan=$total_penjualan + $key->total_harga;
        }

        $start = Carbon::parse($data->tanggal_awal);
        $end = Carbon::parse($data->tanggal_akhir);
        $jarak_hari = $end->diffInDays($start) + 1;

        // cek karyawan
        $data_karyawan=User::select('id','name')
            ->where('toko_id',$user->toko_id)
            ->where('status','y')
            ->where('jenis','karyawan')
            ->get();

        $total_gaji=0;
        foreach ($data_karyawan as $key) {
            $start_date=Carbon::parse($data->tanggal_awal);
            $key['masuk'] = 0;
            $key['libur'] = 0;
            while($start_date <= $end){
                $cek_absen=Absen::where('tanggal',$start_date->toDateString())
                ->where('userid',$key->id)
                ->where('status','masuk')
                ->first();
                if($cek_absen){
                    $key['masuk'] = $key['masuk'] + 1;
                }else{
                    $key['libur'] = $key['libur'] + 1;
                }
                
                $start_date->addDay();
            }
            $key['gaji']=$key['masuk'] * $user->gaji_harian;
            $total_gaji=$total_gaji + $key['gaji'];
        }

        $biaya_sewa=$user->sewa * ($jarak_hari / 30);
        $keuntungan_kotor=$total_penjualan * ($data->keuntungan/100);
        $keuntungan_bersih=$keuntungan_kotor-$biaya_sewa-$total_gaji;
        if($keuntungan_bersih < 0){
            $status='tombok';
        }else{
            $status='untung';
        }
        
        
        return response()->json([
            'status'=>true,
            'hasil'=>$status,
            'keuntungan_bersih'=>$keuntungan_bersih,
            'restok'=>$total_penjualan-$keuntungan_kotor,
            'penjualan'=>$total_penjualan,
            'keuntungan_kotor'=>$keuntungan_kotor,
            'biaya_sewa'=>$biaya_sewa,
            'total_gaji'=>$total_gaji,
            'data_karyawan'=>$data_karyawan,
        ]);
    }
}
