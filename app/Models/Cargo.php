<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function cargoTrackers() {
        return $this->hasMany(CargoTracker::class, 'cargo_id', 'id');
    }

    public function latestCargoTracker() {
        return $this->hasOne(CargoTracker::class, 'cargo_id', 'id')->latest();
    }

    public function route() {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    public function bus() {
        return $this->belongsTo(Bus::class, 'bus_id', 'id');
    }

    public function agent() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
