<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusLayout extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function buses () {
        return $this->hasMany(Bus::class, 'bus_class_id', 'id');
    }
}
