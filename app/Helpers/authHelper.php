<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

if (!function_exists('authUser')){
    function authUser():User{
        return User::find(Auth::user()->id);
    }
}