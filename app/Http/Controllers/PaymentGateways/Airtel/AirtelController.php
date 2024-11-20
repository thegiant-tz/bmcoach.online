<?php

namespace App\Http\Controllers\PaymentGateways\Airtel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class AirtelGatewayController extends Controller
{
    private function Authorization() {
        $url = "https://openapi.airtel.africa/auth/oauth2/token";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            "client_id" => env('AIRTEL_CLIENT_ID'),
            "client_secret" => env('AIRTEL_SECRET_KEY'),
            "grant_type" => "client_credentials"
        )));
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp);
    }
// 
    private function access_token() {
        return $this->Authorization()->access_token;
    }

    private function USSD_Push(Request $req) {
        
            try {
                $url = "https://openapi.airtel.africa/merchant/v1/payments/";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'X-Country: TZ',
                    'x-Currency: TZS',
                    'Content-type: application/json',
                    'Authorization: Bearer ' .$this->access_token()
                ));
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
                    "reference" => $req->ref,
                    "subscriber" => array(
                        "country" => "TZ",
                        "currency" => "TZS",
                        "msisdn" => phoneWithCountryCode($req->phone)
                    ),
                    "transaction" => array(
                        "amount" => $req->amount,
                        "country" => "TZ",
                        "currency" => "TZS",
                        "id" => '23242424242'
                    )
                )));
                //for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
                $response = curl_exec($curl);
                curl_close($curl);
                
                return json_decode($response);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        
    }

    public function airtelMoney(Request $req) {
        $response = $this->USSD_Push($req);
        if ($response->status->response_code === "DP00800001006") {
            $status_code = 200;
            $message = "Transaction in pending state.";
        } elseif ($response->status->response_code === "DP00800001001") {
            $status_code = 200;
            $message = "Transaction is successful.";
        } elseif ($response->status->response_code === "DP01000001000"){
            $status_code = 205;
            $message = "Your payment could not be completed, Please try again";
        } elseif ($response->status->response_code === "DP01000001005"){
            $status_code = 205;
            $message = "Your payment failed, Please try again";
        } elseif ($response->status->response_code === "DP01000001012"){
            $status_code = 203;
            $message = "Mobile number entered is incorrect or not registered";
        } elseif ($response->status->response_code === "DP00800001000") {
            $status_code = 203;
            $message = "External Transaction ID already exists.";
        } else {
            $status_code = 205;
            $message = "Something went wrong, Please try again";
        }
        $feedback = array(
            'status_code' => $status_code,
            'message' => $message
        );
        // return $response;
        return response()->json($feedback);
    }
}

