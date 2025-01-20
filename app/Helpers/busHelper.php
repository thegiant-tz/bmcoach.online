<?php

use Carbon\Carbon;
use App\Models\Bus;
use App\Models\Fare;
use App\Models\Route;
use App\Models\Timetable;

if (!function_exists('getBus')) {
    function getBus($busNumber) {
        return Bus::whereNumber($busNumber)->first();
    }
}

if (!function_exists('getBusSeatLeft')) {
    function getBusSeatLeft(Timetable $timetable) {

        $capacity = $timetable->bus->layout->capacity;
        return $capacity - count($timetable->bookings);
    }
}

if (!function_exists('getFare')) {
    function getFare(Route $route, Bus $bus) {
        $fare = Fare::whereRouteId($route->id)
        ->whereBusClassId($bus->busClass->id)
        ->first();
        if ($fare) {
            return $fare->fare;
        }
        return null;
    }
}