<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RouteController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\BusController;
use App\Http\Controllers\API\RoleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::post('create', [UserController::class, 'createUser'])->name('create');
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::get('roles', [RoleController::class, 'getRoles'])->name('roles');
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Route::get('get-buses', [BookingController::class, 'getBuses'])->name('get.buses');
    Route::group(['prefix' => 'route', 'as' => 'route.'], function () {
        Route::get('list', [RouteController::class, 'routeList'])->name('list');
        Route::post('destinations', [RouteController::class, 'routeDestinations'])->name('destinations');
        Route::post('create', [RouteController::class, 'createRoute'])->name('create');
        Route::post('schedules', [RouteController::class, 'routeSchedules'])->name('schedules');
        Route::post('available-buses', [RouteController::class, 'availableBuses'])->name('available.buses');
    });

    Route::post('store', [BookingController::class, 'store'])->name('store');
    Route::get('list', [BookingController::class, 'list'])->name('list');
    Route::group(['prefix' => 'bus', 'as' => 'bus.'], function() {
        Route::post('schedules', [BusController::class, 'busSchedules'])->name('schedules');
        Route::post('passengers', [BusController::class, 'busPassengers'])->name('passengers');
        Route::post('create', [BusController::class, 'createBus'])->name('create');
    });
});
