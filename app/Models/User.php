<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    function bookings()
    {
        return $this->hasMany(Booking::class, 'agent_id', 'id');
    }

    function cargoTrackers() {
        return $this->hasMany(CargoTracker::class, 'respondent', 'id');
    }

    function cargos() {
        return $this->hasMany(Cargo::class, 'user_id', 'id');
    }

    function boardingPoint()
    {
        return $this->belongsTo(BoardingPoint::class, 'boarding_point_id', 'id');
    }
}
