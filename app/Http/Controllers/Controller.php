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
use Image;
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

    public function base64Image_link($foto,$destination,$namefile)
    {
        $base64Image = trim($foto);
        $rotation=strpos($base64Image, 'image/jp');
        $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
        $base64Image = str_replace(' ', '+', $base64Image);
        $file = base64_decode($base64Image);
        $img = Image::make($file)->orientate();
            if (!empty($rotation)) {
                $exif = exif_read_data($foto);
                if(!empty($exif['Orientation'])) {
                    switch($exif['Orientation']) {
                        case 8:
                            $img->rotate(90);
                            break;
                        case 3:
                            $img->rotate(180);
                            break;
                        case 6:
                            $img->rotate(-90);
                            break;
                    }
                }
            }
        $img->resize(null, 1000, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $img->save($destination.'/'.$namefile);
    }
}
