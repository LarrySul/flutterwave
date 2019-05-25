<?php

namespace App\Http\Controllers;

use App\Service\Log\StoreLog;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function pay(Request $request)
    {
        try{
            \DB::transaction(function () use($request) {
                $transaction = new Transaction();
                $transaction->amount = $request->amount;
                $transaction->currency = $request->currency;
                $transaction->customer_email = $request->email;
                $transaction->customer_name = $request->name;
                $transaction->transaction_reference = $request->transaction_reference;
                $transaction->status = 'pending';
                $transaction->save();
                $message = 'Transaction initiated successfully';
                $route = '/payment/process';
                $this->logInformation($message, $route, $request);
            });
            return ['status' => 'success'];
        }catch (\Exception $exception){
            $message = 'Error occured while initiating transaction';
            $route = '/payment/process';
            $this->logInformation($message, $route, $request);
            return ['status' => 'error'];
        }

    }

    public function updateTransaction(Request $request, $transaction_reference)
    {
        try{
            \DB::transaction(function () use ($request, $transaction_reference) {
                $transaction = Transaction::where('transaction_reference', $transaction_reference)->first();
                $transaction->account_id = $request['transaction_response']['tx']['AccountId'];
                $transaction->ip_address = $request['transaction_response']['tx']['IP'];
                $transaction->charged_amount = $request['transaction_response']['tx']['charged_amount'];
                $transaction->customer_id = $request['transaction_response']['tx']['customerId'];
                $transaction->payment_type = $request['transaction_response']['tx']['paymentType'];
                $transaction->rave_reference = $request['transaction_response']['tx']['raveRef'];
                $transaction->status = $request['transaction_response']['tx']['status'];
                $transaction->save();

                $message = 'Transaction updated successfully';
                $route = '/payment/process/update/'.$transaction_reference;
                $this->logInformation($message, $route, $request);
            });
            return ['status' => 'success'];
        }catch (\Exception $exception){
            $message = 'Error occured while updating transaction';
            $route = '/payment/process/update/'.$transaction_reference;
            $this->logInformation($message, $route, $request);
            return ['status' => 'error'];
        }
    }

    private function logInformation($message, $route, $request)
    {
        $user_agent = $request->header('User-Agent');
        $log_service = new StoreLog($message, $route, $user_agent);
        $log_service->storeLogInformation();
    }
}
