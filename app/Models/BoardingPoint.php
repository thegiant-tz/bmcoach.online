<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingPoint extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function route() {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    function users()
    {
        return $this->hasMany(User::class, 'agent_id', 'id');
    }

    function boardingPointbookings()
    {
        return $this->hasMany(Booking::class, 'boarding_point_id', 'id');
    }
    
    function droppingPointBookings()
    {
        return $this->hasMany(Booking::class, 'dropping_point_id', 'id');
    }
}
