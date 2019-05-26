<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\raveRequest;
use Auth\Validator;
use App\User;
use Rave;


class raveController extends Controller
{   
    protected $url = 'https://ravesandboxapi.flutterwave.com/v2/kyc/bvn';


    public function index(Request $request){
        $user = new User;
        $key = 'FLWSECK-ef97e95ae83887c170b8f83e5a0334e8-X';
        $user->bvn = $request->bvn;
        $bvnUser = json_encode($user);
        $bvnNumber = explode(':',str_replace(['"','}'],'', $bvnUser))[1];

        $result = $this->url . '/'. $bvnNumber. '?seckey='. $key;

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $result); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36'); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $head = curl_exec($ch); 

        return redirect('/status')->with('status', $head);
    }

}
