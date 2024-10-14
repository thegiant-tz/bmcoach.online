<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
