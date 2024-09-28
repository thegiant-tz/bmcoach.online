<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusRoute extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function bus() {
        return $this->belongsTo(Bus::class, 'bus_id', 'id');
    }

    function route() {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    function bookings() {
        return $this->hasMany(Booking::class, 'bus_route_id', 'id');
    }
}