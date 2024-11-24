<?php

namespace App\Http\Controllers\PaymentGateways;

use Illuminate\Http\Request;
use Thegiant\Azampay\AzamPay;
use App\Http\Controllers\Controller;
use Alphaolomi\Azampay\AzampayService;
use App\Http\Controllers\BLSMS;

class AzampayController extends Controller
{
    function paymentFeedback(Request $request) {
        BLSMS::_sendMessageBLSM('payment', '0782889935');
    }
    function mnoCheckout(Request $request)
    {
        $externalId = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        // return AzamPay::mnoCheckout($request->accountNumber, $request->amount, $request->provider, $externalId);
        $azampay = new AzampayService();

        $data = $azampay->mobileCheckout([
            'amount' => 1000,
            'currency' => 'TZS',
            'accountNumber' => '0625933171',
            'externalId' => $externalId,
            'provider' => 'Mpesa',
        ]);

        return $data;
    }
}
