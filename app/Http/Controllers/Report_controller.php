<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absen;
use App\Models\Report;
use App\Models\Gaji_report;
use Session;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\Trans_controller;

class Report_controller extends Controller
{

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
            // ->where('status','y')
            ->where('jenis','karyawan')
            ->get();

        $total_gaji=0;
        $karyawan=[];
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
            if(!empty($key['masuk'])){
                $karyawan[]=$key;
            }
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
            'data_karyawan'=>$karyawan,
        ]);
    }

    public function save_report(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'tgl_awal' => 'required',
            'tgl_akhir' => 'required',
            'status' => 'required',
            // 'keuntungan_kotor' => 'required',
            // 'keuntungan_bersih' => 'required',
            // 'total_penjualan' => 'required',
            // 'restok' => 'required',
            // 'gaji_sewa' => 'required',
            // 'total_gaji' => 'required',
            // 'biaya_sewa' => 'required',
            // 'karyawan' => 'required',
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
        
        $karyawan=json_decode($data->karyawan,true);
        DB::beginTransaction();
        try {
            unset($data['session']);
            unset($data['karyawan']);
            $data['user_id']=$user->id;
            $data['toko_id']=$user->toko_id;
            $report=Report::create($data->all());
            
            foreach ($karyawan as $key) {
                Gaji_report::create([
                    'jumlah'    => $key['gaji'],
                    'user_id'   =>  $key['id'],
                    'masuk'   =>  $key['masuk'],
                    'libur'   =>  $key['libur'],
                    'report_id' =>$report->id
                ]);
            }

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }

    public function get_report(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'toko_id' => 'required',
            'tgl_awal' => 'required',
            'tgl_akhir' => 'required',
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
            $report=Report::where('toko_id',$data->toko_id)
                ->where('tgl_awal','>=',$data->tgl_awal)
                ->where('tgl_akhir','<=',$data->tgl_akhir)
                ->get();
            foreach ($report as $key) {
                $key->karyawan=Gaji_report::select(
                        'gaji_reports.*',
                        'users.name',
                    )
                    ->join('users','users.id','=','gaji_reports.user_id')
                    ->where('report_id',$key->id)
                    ->get();
            }

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil','data'=>$report]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }

    public function del_report(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'id_report' => 'required',
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
        
        $karyawan=json_decode($data->karyawan,true);
        DB::beginTransaction();
        try {
            Report::find($data->id_report)->delete();

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }

    public function endis_gaji(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'id_gajireport' => 'required',
            'jenis' => 'required',
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
        
        $karyawan=json_decode($data->karyawan,true);
        DB::beginTransaction();
        try {
            if($data->jenis == 'sudah'){
                Gaji_report::find($data->id_gajireport)->update(['bayar' => 'y']);
            }else if($data->jenis == 'belum'){
                Gaji_report::find($data->id_gajireport)->update(['bayar' => 'n']);
            }

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }
}
