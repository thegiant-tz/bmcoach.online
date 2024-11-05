<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

if (!function_exists('authUser')){
    function authUser():User{
        return User::find(Auth::user()->id);
    }
}

if (!function_exists('isAgent')){
    function isAgent():bool{
        return User::find(Auth::user()->id)->role->name == 'agent';
    }
}

if (!function_exists('isAdmin')){
    function isAdmin():bool{
        return User::find(Auth::user()->id)->role->name == 'admin';
    }
}


if (!function_exists('isCashier')){
    function isCashier():bool{
        return User::find(Auth::user()->id)->role->name == 'cashier';
    }
}
