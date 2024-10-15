<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    function bookedSeats(Request $request)
    {
        $timetable = Timetable::find($request->timeTableId);
        $bookings = $timetable->bus->bookings()
            ->where('dep_date', $timetable->dep_time)
            ->get();
        $seats = [];
        foreach ($bookings as $booking) {
            $seats[$booking->seat_no] = $booking->status;
        }
        return $seats;
    }
}
