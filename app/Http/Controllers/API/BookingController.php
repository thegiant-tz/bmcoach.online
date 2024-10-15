<?php

namespace App\Http\Controllers\API;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    function getBuses(Request $request)
    {
        return Bus::order();
    }

    function store(Request $request)
    {
        try {
            // $route = Route::whereFrom($request->from)->whereTo($request->to)->first();
            // $bus = Bus::whereNumber($request->bus_no)->first();
            $timetable = Timetable::find($request->timetableId);
            $booking = Booking::updateOrCreate([
                'timetable_id' => $timetable->id,
                'agent_id' => Auth::user()->id,
                'status' => 'Processing'
            ], [
                'route_id' => $timetable->route->id,
                'timetable_id' => $timetable->id,
                'bus_id' => $timetable->bus->id,
                'agent_id' => Auth::user()->id,
                'psg_name' => $request->psg_name ?? null,
                'psg_phone' => $request->psg_phone ?? null,
                'fare' => getFare($timetable->route, $timetable->bus),
                'dep_date' => $timetable->dep_time,
                'dep_time' => Carbon::parse($timetable->dep_time)->format('H:i:s'),
                'seat_no' => $request->seat_no,
                'status' => $request->status ?? 'Processing'
            ]);

            if ($booking) {
                return response()->json([
                    'status' => 'success',
                    'statusCode' => env('STATUS_CODE_PREFIX') . '200',
                    'booking' => $booking
                ], 200);
            }
            return response()->json([
                'status' => 'failed',
                'statusCode' => env('STATUS_CODE_PREFIX') . '400'
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'something went wrong',
                'statusCode' => env('STATUS_CODE_PREFIX') . '500',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    function destroy(Request $request)
    {
        try {
            Booking::find($request->bookingId)->delete();
            return response()->json([
                'status' => 'success',
                'statusCode' => env('STATUS_CODE_PREFIX') . '200',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'something went wrong',
                'statusCode' => env('STATUS_CODE_PREFIX') . '500',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    function list(Request $request)
    {
        $bookings = Booking::when(isset($request->depDate) && isset($request->busId), fn($query) => $query->where('DATE(dep_date)', $request->dep_date)->whereBusId($request->busId))
            ->whereIn('route_id', [1])
            ->groupBy('dep_date')->orderBy('id', 'desc')->get();
        return BookingResource::collection($bookings)->resolve();
    }
}
