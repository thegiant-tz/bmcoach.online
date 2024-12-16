<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RouteController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\BusController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TimetableController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\PaymentGateways\AzampayController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::post('create', [UserController::class, 'createUser'])->name('create');
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::post('logout', [UserController::class, 'logout'])->name('logout');
    Route::post('list', [UserController::class, 'list'])->name('list');
    Route::get('roles', [RoleController::class, 'getRoles'])->name('roles');
});

Route::group(['prefix' => 'route', 'as' => 'route.'], function () {
    Route::get('list', [RouteController::class, 'routeList'])->name('list');
    Route::post('boarding-points', [RouteController::class, 'boardingPoints']);
    Route::post('create-boarding-point', [RouteController::class, 'createBoardingPoint']);
    Route::post('fares', [RouteController::class, 'routeFares']);
    Route::post('fare/create', [RouteController::class, 'routeFareCreate']);
    Route::post('timetable', [RouteController::class, 'routeTimetable'])->name('timetable');
    Route::post('timetable/grouped', [RouteController::class, 'routeGroupedTimetable']);
    Route::post('timetable/state', [TimetableController::class, 'setTimetableState']);
    Route::get('all', [RouteController::class, 'list'])->name('list.all');
    Route::post('destinations', [RouteController::class, 'routeDestinations'])->name('destinations');
    Route::post('schedules', [RouteController::class, 'routeSchedules'])->name('schedules');
    Route::post('available-buses', [RouteController::class, 'availableBuses'])->name('available.buses');
});

Route::group(['prefix' => 'timetable', 'as' => 'timetable.'], function () {
    Route::post('booked-seats', [TimetableController::class, 'bookedSeats']);
    Route::post('create', [TimetableController::class, 'create']);
    Route::post('bookings/{agentId?}', [TimetableController::class, 'bookings'])->middleware('auth:sanctum');
});
Route::post('passenger/store', [BookingController::class, 'store'])->name('store');

Route::group(['middleware' => 'auth:sanctum'], function () {
    // Route::get('get-buses', [BookingController::class, 'getBuses'])->name('get.buses');
    Route::group(['prefix' => 'route', 'as' => 'route.'], function () {
        Route::post('create', [RouteController::class, 'createRoute'])->name('create');
        Route::post('get-unassigned-buses', [RouteController::class, 'getUnassignedBuses'])->name('unassigned.buses');
        Route::post('assign-bus', [RouteController::class, 'assignBusToRoute'])->name('assigne.bus');
    });
    Route::post('store', [BookingController::class, 'store'])->name('store');
    Route::post('destroy', [BookingController::class, 'destroy'])->name('destroy');
    Route::post('list', [BookingController::class, 'list'])->name('list');
    Route::post('timetable-agents-list', [BookingController::class, 'agentTimetableCollection']);
    Route::group(['prefix' => 'bus', 'as' => 'bus.'], function () {
        Route::post('schedules', [BusController::class, 'busSchedules'])->name('schedules');
        Route::post('passengers', [BusController::class, 'busPassengers'])->name('passengers');
        Route::post('create', [BusController::class, 'createBus'])->name('create');
        Route::get('list', [BusController::class, 'list'])->name('list');
        Route::get('layouts', [BusController::class, 'busLayouts']);
        Route::get('classes', [BusController::class, 'busClasses']);
    });

    Route::group(['prefix' => 'cargo', 'as' => 'cargo.'], function() {
        Route::post('store', [CargoController::class, 'store']);
        Route::post('list/{status?}', [CargoController::class, 'list']);
        Route::post('boarding', [CargoController::class, 'boarding']);
    });
});

Route::post('test', [AzampayController::class, 'mnoCheckout']);
Route::post('payment/feedback', [AzampayController::class, 'paymentFeedback']);
