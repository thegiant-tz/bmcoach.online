<?php

use App\Models\User;
use App\Models\Route;
use Illuminate\Support\Str;
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

if (!function_exists('phoneWithCountryCode')) {
    function phoneWithCountryCode($phone, $callingCode = null)
    {
        if (is_null($callingCode)) {
            $callingCode = defaultCountryCode();
        }
        $phone = replaceSpaceWith($phone, ' ', '');
        $removal = str_startsWith($phone, '+0') ? '+0': (str_startsWith($phone, "0") ? '0': (!str_startsWith($phone, '+') ? '+':''));
        return Str::replaceFirst($removal, $callingCode, $phone);
    }
}

if (!function_exists('replaceSpaceWith')) {
    function replaceSpaceWith($string, $remove, $place)
    {
        return $string = str_replace($remove, $place,  $string); # Replaces all spaces with char.
    }
}

if (!function_exists('str_startsWith')) {
    function str_startsWith($str, $start_with)
    {
        return Str::startsWith($str, $start_with);
    }
}


if (!function_exists('codeIdToId')) {
    function codeIdToId($codeId, $isCargo = true)
    {
        return (int)str_replace($isCargo ? 'BMC' : 'BM', '', $codeId);
    }
}

