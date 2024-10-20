<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fare extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    function busClass()
    {
        return $this->belongsTo(BusClass::class, 'bus_class_id', 'id');
    }

    
}
