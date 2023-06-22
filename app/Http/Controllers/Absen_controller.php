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
            'foto' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        $safeName = 'absen_'.$user->id.'_'.strtotime(Carbon::now()).'.jpg';
        $this->base64Image_link($data->foto,public_path('/storage/absen'),$safeName);
        $data['foto']=$safeName;
        $cek=Absen::where('userid',$user->id)
            ->where('tanggal',$data->tanggal)
            ->whereNull('updated_at')
            ->first();
        if($cek){
            $cek->update([
                'userid' => $user->id,
                'tanggal' => $data->tanggal,
                'status' => $data->status,
                'foto' => $data->foto,
                'updated_at' => Carbon::now(),
            ]);
        }else{
            $cek=Absen::where('userid',$user->id)
                ->where('tanggal',$data->tanggal)
                ->first();
            if(!$cek){
                Absen::create([
                    'userid' => $user->id,
                    'tanggal' => $data->tanggal,
                    'status' => $data->status,
                    'foto' => $data->foto,
                    'created_at' => Carbon::now(),
                ]);
            }else{
                return response()->json(['status'=>false,'message'=>'Sudah absen masuk dan pulang']);
            }
        }
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
        
        $data_absen=Absen::select(
                '*',
                DB::raw("CASE WHEN foto = '' THEN NULL ELSE CONCAT('".url('/storage/absen')."','/',foto) END AS foto"),
            )
            ->where('userid',$user->id)
            ->whereBetween('tanggal', [$data->tanggal_awal . ' 00:00:00', $data->tanggal_akhir . ' 23:59:59'])
            ->get();
        return response()->json(['status'=>true,'data'=>$data_absen]);
    }

    public function get_absen_all(Request $data)
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
        
        $data_absen=[];
        $start_date=Carbon::parse($data->tanggal_awal);
        $key['masuk'] = 0;
        $key['libur'] = 0;
        while($start_date <= Carbon::parse($data->tanggal_akhir)){
            
            $data_user=User::where('status','y')
                ->where('toko_id',$user->toko_id)
                ->where('jenis','karyawan')
                ->get();
            foreach ($data_user as $key) {
                $absen=Absen::select(
                        '*',
                        DB::raw("CASE WHEN foto = '' THEN NULL ELSE CONCAT('".url('/storage/absen')."','/',foto) END AS foto"),
                    )
                    ->where('tanggal',$start_date->toDateString())
                    ->where('userid',$key->id)
                    ->first();
                if($absen){
                    $absen['name']=$key->name;
                    $absen['tanggal'] = $this->convertToIndonesianDate($start_date->toDateString());
                    $data_absen[]=$absen;
                }else{
                    $data_absen[]=[
                        'id' => null,
                        'userid' => $key->id,
                        'tanggal' => $this->convertToIndonesianDate($start_date->toDateString()),
                        'foto' => null,
                        'status' => 'libur',
                        'created_at' => null,
                        'updated_at' => null,
                        'name' => $key->name,
                    ];
                }
            }

            $start_date->addDay();
        }

        return response()->json(['status'=>true,'data'=>$data_absen]);
    }

    function convertToIndonesianDate($date) {
        $days = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );

        $months = array(
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );
    
        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->format('l');
    
        $day = $dateObj->format('d');
        $month = $months[(int)$dateObj->format('m')];
        $year = $dateObj->format('Y');
    
        $indonesianDate = $days[$dayOfWeek] . ', ' . $day . ' ' . $month . ' ' . $year;
    
        return $indonesianDate;
    }
}
