<?php

namespace App\Http\Controllers\PaymentGateways;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Thegiant\Azampay\AzamPay;

class AzampayController extends Controller
{
    public function authenticate(Request $request) {
        return AzamPay::authenticate();
    }
}
