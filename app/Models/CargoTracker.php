<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargoTracker extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cargo() {
        return $this->belongsTo(Cargo::class, 'cargo_id', 'id');
    }

    public function agent() {
        return $this->belongsTo(User::class, 'respondent', 'id');
    }

    public function bus() {
        return $this->belongsTo(Bus::class, 'bus_id', 'id');
    }
}
