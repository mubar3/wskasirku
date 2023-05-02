<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Auth_controller extends Controller
{
    public function register(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'toko_id' => 'required',
            'jenis' => 'required',
            'key' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        
        DB::beginTransaction();
        try {
            if(User::where('email',$data->email)->first()){
                return response()->json(['status'=>false,'message'=>'Data sudah ada']);
            }
            $insert=User::create([
                'name' => $data->name,
                'email' => $data->email,
                'toko_id' => $data->toko_id,
                'jeni' => $data->jenis,
                'key' => $data->key,
                'password' => $this->encryptHash($data->password,$data->key),
            ]);

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
        
    }
    
    public function login(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('email',$data->email)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Email salah']);
        }
        $user=User::where('password',$this->encryptHash($data->password,$user->key))->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Password salah']);
        }
        $token=$this->get_session();
        $user->update([
            'session' => $token
        ]);
        return response()->json(['status'=>true,'message'=>'Berhasil','token'=>$token]);
    }

    public function logout(Request $data)
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
        $user->update([
            'session' => ''
        ]);
        return response()->json(['status'=>true,'message'=>'Berhasil']);
    }
}
