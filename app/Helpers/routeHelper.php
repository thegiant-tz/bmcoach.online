<?php

use App\Models\Route;

if (!function_exists('getRouteInstance')) {
    function getRouteInstance($from, $to) {
        return Route::where('from', strtoupper($from))
        ->where('to', strtoupper($to))->first();
    }
}