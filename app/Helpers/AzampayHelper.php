<?php 

if (!function_exists('isLiveMode')) {
    function isLiveMode() {
        return strtolower(env('AZAMPAY_MODE')) == 'live';
    }
}