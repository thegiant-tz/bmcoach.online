<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id', 'id');
    }

    function bookings()
    {
        return $this->hasMany(Booking::class, 'timetable_id', 'id');
    }
}
