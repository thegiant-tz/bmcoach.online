<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteSchedule extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }
}
