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
        $key = 'FLWSECK-ec8d64d741860e0f120dd8aad18b5835-X';
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


    public function initialize(){
        Rave::initialize(route('callback'));
    }

    public function callback(Request $data) {
        $data = Rave::verifyTransaction(request()->txref);

        $chargeResponsecode = $data->data->chargecode;
        $chargeAmount = $data->data->amount;
        $chargeCurrency = $data->data->currency;
        $customerEmail = $data->data->email;

        // split parameters
        $merchantID = 'RS_357C990D07DA12A79702798AD2C2FB1F';
        $transaction_charge_type = 'percentage';
        $transaction_charge = 0.5;    

        if ($chargeResponsecode == "00" || $chargeResponsecode == "0") {
            
            
            return redirect('/status')->with('status', 'Payment Successful');
    
        } else {
            //Dont Give Value and return to Failure page
        
            return redirect('/failed')->with('status', 'Unable to Complete Payment');
        }
        
    }
}
