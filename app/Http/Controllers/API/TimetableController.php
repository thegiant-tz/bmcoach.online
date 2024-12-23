<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
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

    function create(Request $request)
    {
        try {
            $route = getRouteInstance($request->from, $request->to);
            $bus = getBus($request->busNo);
            $timetable = Timetable::updateOrCreate(
                [
                    'bus_id' => $bus->id,
                    'route_id' => $route->id,
                    'dep_time' => $request->depDate,
                ]
            );
            if ($timetable) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Timetable created successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Timetable failed to be create'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Something went wrong!'
            ]);
        }
    }

    function setTimetableState(Request $request)
    {
        // return response()->json([
        //     'message' => $request->all()
        // ]);

        $updated = Timetable::whereId($request->timetableId)->update([
            'is_active' => (bool) $request->is_active
        ]);
        if ($updated) {
            return response()->json([
                'message' => 'Status: ' . ((bool)$request->is_active ? 'Active' : 'Inactive')
            ]);
        } else {
            return response()->json([
                'message' => 'State not changed'
            ]);
        }
    }

    function bookings(Request $request)
    {
        try {
            $timetable = Timetable::find($request->timetableId);
            $bookings = $timetable->bookings()->when(!is_null(($agentId = $request->agentId)), fn($query) => $query->whereAgentId($agentId))->get();
            return response([
                'status' => 'success',
                'bookings' => BookingResource::collection($bookings)->resolve()
            ]);
        } catch (\Throwable $th) {
            return response([
                'status' => 'failed',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
