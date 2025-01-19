<?php

use App\Models\Route;
use Illuminate\Http\Request;

if (!function_exists('getRouteInstance')) {
    function getRouteInstance(Request $request) {
        return  isset($request->routeId) ? Route::find(aes_decrypt($request->routeId)) : Route::where('from', strtoupper($request->from))
        ->where('to', strtoupper($request->to))->first();
    }
}