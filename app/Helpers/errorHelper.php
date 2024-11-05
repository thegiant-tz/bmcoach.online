<?php

if (!function_exists('errorResponse')) {
    function errorResponse(Throwable $th) {
        return response()->json([
            'status' => 'something went wrong',
            'statusCode' => env('STATUS_CODE_PREFIX') . '500',
            'error' => $th->getMessage()
        ], 500);
    }
}