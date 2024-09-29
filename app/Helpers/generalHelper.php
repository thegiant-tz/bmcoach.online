<?php

use App\Models\Route;
use App\Models\User;
use Illuminate\Http\Request;

if (!function_exists('validRecipient')) {
    function validRecipient($recipient)
    {
        if (strlen($recipient)) {
            if (substr($recipient, 0, 4) == defaultCountryCode()) {
                $recipient = str_replace(defaultCountryCode(), '255', $recipient);
            } elseif (substr($recipient, 0, 1) == '0') {
                $recipient = '255' . substr($recipient, 1, 9);
            } elseif (substr($recipient, 0, 3) == "255") {
            } else {
                return false;
            }
        } else {
            return false;
        }
        return $recipient;
    }
}

if (!function_exists('defaultCounrtyCode')) {
    function defaultCountryCode()
    {
        return '+255';
    }
}

if (!function_exists('requestAdd')) {
    function requestAdd(Request &$request, array $parameters)
    {
        // $newRequest = new Request();
        $request->merge($parameters);
        // $newRequest->replace(array_merge($parameters, $request->all()));
        // $newRequest->s

    }
}

if (!function_exists('fullRoute')) {
    function fullRoute(Route $route)
    {
        return $route->from . ' - ' . $route->to;
    }
}


if (!function_exists('createUsername')) {
    function createUsername()
    {
        $latestUser = User::latest()->first();
        return 'BM'.str_pad(is_null($latestUser) ? 1 : $latestUser->id + 1, 4, '0', STR_PAD_LEFT);
    }
}
