<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function busRoutes()
    {
        return $this->hasMany(BusRoute::class, 'route_id', 'id');
    }

    function schedules()
    {
        return $this->hasMany(RouteSchedule::class, 'route_id', 'id');
    }

    function bookings()
    {
        return $this->hasMany(Booking::class, 'route_id', 'id');
    }

    function timetables()
    {
        return $this->hasMany(Timetable::class, 'route_id', 'id');
    }

    function fares()
    {
        return $this->hasMany(Fare::class, 'route_id', 'id');
    }

    function boardingPoints() {
        return $this->hasMany(BoardingPoint::class, 'route_id', 'id')->orderBy('point', 'ASC');
    }
}
