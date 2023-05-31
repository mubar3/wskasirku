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
}
