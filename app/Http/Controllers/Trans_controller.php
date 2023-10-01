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
            ->orderBy('nama','asc')
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
                $key->status=$key->status == 'y' ? true : false;
            }
        }else{
            foreach ($barang as $key) {
                $key->status=$key->status == 'y' ? true : false;
            }
        }
        return response()->json(['status'=>true,'message'=>'sukses','data'=>$barang]);
    }

    public function status_barang(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'barang_id' => 'required',
            'status' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        Toko_barang::find($data->barang_id)->update(['status' => $data->status]);
        return response()->json(['status'=>true,'message'=>'Sukses']);
    }
    public function get_barangall(Request $data)
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
            ->orderBy('created_at','desc')
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
                $key->status=$key->status == 'y' ? true : false;
            }
        }else{
            foreach ($barang as $key) {
                $key->status=$key->status == 'y' ? true : false;
            }
        }
        return response()->json(['status'=>true,'message'=>'sukses','data'=>$barang]);
    }

    public function add_barang(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'toko_id' => 'required',
            'nama' => 'required',
            'harga' => 'required',
            // 'foto' => 'required',
            // 'stok' => 'required',
            // 'tak_terbatas' => 'required',
            // 'status' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        if(!empty($data->stok)){
            $data['tak_terbatas']='n';
        }else{
            unset($data['stok']);
        }
        unset($data['session']);
        if(Toko_barang::create($data->all())){
            return response()->json(['status'=>true,'message'=>'Sukses']);
        }
    }

    public function add_stok(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'barang_id' => 'required',
            // 'nama' => 'required',
            // 'harga' => 'required',
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
            $cek_barang=Toko_barang::find($data->barang_id);
            if(!empty($cek_barang->stok)){
                $cek_barang->update([
                    'stok' => $cek_barang->stok - 1
                ]);
            }
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

    public function edit_barang(Request $data)
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
        if(!empty($data->stok)){
            $data['tak_terbatas']='n';
        }else{
            $data['tak_terbatas']='y';
            unset($data['session']);
            // unset($data['stok']);
        }
        $data['id']=$data->barang_id;
        unset($data['session']);
        unset($data['barang_id']);
        // return $data->all();
        if(Toko_barang::find($data->id)->update($data->all())){
            return response()->json(['status'=>true,'message'=>'Sukses']);
        }else{
            return response()->json(['status'=>false,'message'=>'Gagal']);
        }
    }

    public function hapus_barang(Request $data)
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
        
        if(Toko_barang::find($data->barang_id)->delete()){
            return response()->json(['status'=>true,'message'=>'Sukses']);
        }else{
            return response()->json(['status'=>false,'message'=>'Gagal']);
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
            $cek_barang=Toko_barang::find($data->barang_id);
            if(!empty($cek_barang->stok)){
                $cek_barang->update([
                    'stok' => $cek_barang->stok + 1
                ]);
            }
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

    public function detail_penjualan_new(Request $data)
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
        $penjualan_tambah=(int)((Toko2barang_tran::select(
                    DB::raw('sum(toko2barang_trans.jumlah) as jumlah')
                )
                ->where('toko2_trans.toko_id',$user->toko_id)
                ->whereBetween('toko2barang_trans.created_at',[$data->tanggal_awal,Carbon::parse($data->tanggal_akhir)->addDay()])
                ->join('toko2_trans','toko2_trans.id','=','toko2barang_trans.trans_id')
                ->first())->jumlah);

        return response()->json([
            'status'=>true,
            'message'=>'Berhasil',
            'tambah'=>$penjualan_tambah,
            'kurang'=>0,
            'total'=>$penjualan_tambah,
            'detail'=>Toko2barang_tran::select(
                    'toko2barang_trans.*',
                    'toko_barangs.nama',
                    'toko2barang_trans.harga',
                )
                ->where('toko2_trans.toko_id',$user->toko_id)
                ->whereBetween('toko2barang_trans.created_at',[$data->tanggal_awal,Carbon::parse($data->tanggal_akhir)->addDay()])
                ->join('toko2_trans','toko2_trans.id','=','toko2barang_trans.trans_id')
                ->join('toko_barangs','toko_barangs.id','=','toko2barang_trans.barang_id')
                ->get(),
        ]);
    }

    public function pembelian(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'session' => 'required',
            'nama' => 'required',
            'barang' => 'required',
        ]);
        if($validator->fails()){      
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }
        $user=User::where('session',$data->session)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Session tidak tersedia']);
        }
        $barang=json_decode($data->barang,true);
        // return response()->json(['status'=>false,'message'=>$barang]);
        
        DB::beginTransaction();
        try {
            $insert=Toko2_tran::create([
                'nama' => $data->nama,
                'toko_id' => $user->toko_id,
                'userid' => $user->id
            ]);
            foreach ($barang as $key) {
                $barang=Toko_barang::where('id',$key['id']);
                $data_barang=$barang->first();
                $cek_barang=$barang->whereNotNull('stok')->first();
                if($cek_barang){
                    if($cek_barang->stok < $key['banyak']){
                        return response()->json(['status'=>false,'message'=>'Stok barang ada yang kurang']);
                    }
                    $cek_barang->update([
                        'stok' => $cek_barang->stok - $key['banyak']
                    ]);
                }

                // kurangi stok bahan
                $bahan2=Stok_bahan::where('barang_id',$key['id'])->get();
                foreach ($bahan2 as $value) {
                    $cek_bahan=Bahan::find($value->bahan_id);
                    $stok_akhir=(int)$cek_bahan->stok_gr - ((int)$value->takar_gr * $key['banyak']);
                    $cek_bahan->update([
                        'stok_gr' => $stok_akhir
                    ]);
                }

                toko2barang_tran::create([
                    'trans_id' => $insert->id,
                    'barang_id' => $key['id'],
                    'jumlah' => $key['banyak'],
                    // 'harga' => !empty($key['harga']) ? $key['harga'] : $data_barang->harga,
                    'harga' =>  str_replace([',','.'],'',$key['harga']),
                ]);
            }

            DB::commit();
            return response()->json(['status'=>true,'message'=>'Berhasil']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Terjadi Kesalahan dalam penyimpanan data']);
        }
    }

    public function get_barang_new(Request $data)
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
            ->where('status','y');
            
        if(isset($data->cari) && !empty($data->cari)){
            $barang=$barang->where('nama','like','%'.$data->cari.'%')
                    ->orwhere('harga','like','%'.$data->cari.'%');
        }
        $barang=$barang->orderBy('nama','asc')
            ->get();

        if(!empty($data->tanggal_awal)){
            foreach ($barang as $key) {
                $key->tambah=(int)((Toko2barang_tran::select(
                    DB::raw('sum(toko2barang_trans.jumlah) as jumlah')
                )
                ->where('toko2barang_trans.barang_id',$key->id)
                ->where('toko2_trans.toko_id',$user->toko_id)
                ->whereBetween('toko2barang_trans.created_at',[$data->tanggal_awal,Carbon::parse($data->tanggal_akhir)->addDay()])
                ->join('toko2_trans','toko2_trans.id','=','toko2barang_trans.trans_id')
                ->first())->jumlah);

                $key->kurang=0;
                $key->status=$key->status == 'y' ? true : false;
            }
        }else{
            foreach ($barang as $key) {
                $key->status=$key->status == 'y' ? true : false;
            }
        }
        return response()->json(['status'=>true,'message'=>'sukses','data'=>$barang]);
    }

    public function get_transaksi(Request $data)
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

        $data=Toko2_tran::select(
                '*',
                DB::raw('DATE_FORMAT(created_at, "%d %M %Y") as tanggal'),
                DB::raw('DATE_FORMAT(created_at, "%H:%i") as waktu'),
            )
            ->whereBetween('toko2_trans.created_at',[$data->tanggal_awal,Carbon::parse($data->tanggal_akhir)->addDay()])
            ->where('toko2_trans.toko_id',$user->toko_id)
            ->get();
        foreach ($data as $key) {
            $key->barang=Toko2barang_tran::select(
                    'toko2barang_trans.*',
                    'toko_barangs.nama',
                    'toko_barangs.is_produk',
                    // 'toko_barangs.harga',
                    'toko2barang_trans.harga',
                    // DB::raw('toko_barangs.harga * toko2barang_trans.jumlah as total_harga')
                    DB::raw('toko2barang_trans.harga * toko2barang_trans.jumlah as total_harga')
                )
                ->where('toko2barang_trans.trans_id',$key->id)
                ->join('toko_barangs','toko_barangs.id','=','toko2barang_trans.barang_id')
                ->get();
            $harga=0;
            foreach ($key->barang as $value) {
                $harga=$harga+$value->total_harga;
            }
            if(count($key->barang) < 1){
                Toko2_tran::find($key->id)->delete();
            }
            $key->total_harga=$harga;
        }
        return response()->json(['status'=>true,'data'=>$data]);
    }
}
