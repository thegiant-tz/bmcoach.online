<?php

namespace App\Models;

use App\Models\Cargo;
use App\Models\CargoTracker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bus extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function bookings()
    {
        return $this->hasMany(Booking::class, 'bus_id', 'id');
    }

    function timetables()
    {
        return $this->hasMany(Timetable::class, 'bus_id', 'id');
    }

    function busClass() {
        return $this->belongsTo(BusClass::class, 'bus_class_id', 'id');
    }

    function layout() {
        return $this->belongsTo(BusLayout::class, 'bus_layout_id', 'id');
    }

    function cargos() {
        return $this->hasMany(Cargo::class, 'bus_id', 'id');
    }

    function cargoTrackers() {
        return $this->hasMany(CargoTracker::class, 'respondent', 'id');
    }
}
