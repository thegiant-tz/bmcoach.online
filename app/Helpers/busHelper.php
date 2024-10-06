<?php

use App\Models\Bus;

if (!function_exists('getBus')) {
    function getBus($busNumber) {
        return Bus::whereNumber($busNumber)->first();
    }
}