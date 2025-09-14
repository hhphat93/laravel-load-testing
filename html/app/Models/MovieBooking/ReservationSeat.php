<?php

namespace App\Models\MovieBooking;

use Illuminate\Database\Eloquent\Model;

class ReservationSeat extends Model
{
    protected $connection = 'movie_booking';
    
    protected $fillable = [
        'user_id',
        'showtime_id',
        'seat_id',
        'status',
        'lock_expires_at',
    ];
}
