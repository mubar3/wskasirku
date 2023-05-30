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
                'jenis' => $data->jenis,
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
        $user=User::where('email',$data->email)->where('password',$this->encryptHash($data->password,$user->key))->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Password salah']);
        }
        if($user->status == 'n'){
            return response()->json(['status'=>false,'message'=>'User nonaktif']);
        }
        $token=$this->get_session();
        $user->update([
            'session' => $token
        ]);
        return response()->json(['status'=>true,'message'=>'Berhasil','token'=>$token]);
    }

    public function ubah_data(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'email' => 'required',
            'password' => 'required',
            'password_lama' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        if($this->encryptHash($data->password_lama,$user->key) != $user->password){
            return response()->json(['status'=>false,'message'=>'Password yang lama salah']);
        }
        
        if($this->encryptHash($data->password,$user->key) == $user->password){
            return response()->json(['status'=>false,'message'=>'Password sama dengan sebelumya']);
        }

        unset($data['session']);
        unset($data['password_lama']);
        $data   ['password']=$this->encryptHash($data->password,$user->key);

        if($user->update($data->all())){
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        }
        return response()->json(['status'=>false,'message'=>'Gagal']);

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

    public function get_data(Request $data)
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
        return response()->json(['status'=>true,'message'=>'Sukses','data'=>$user]);
        
    }
}
