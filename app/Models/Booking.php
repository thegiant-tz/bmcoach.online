<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function bus() {
        return $this->belongsTo(Bus::class, 'bus_id', 'id');
    }

    function route() {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    function agent() {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }
}
