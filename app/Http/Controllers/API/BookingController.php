<?php

namespace App\Http\Controllers\API;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
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
            $route = Route::whereFrom($request->from)->whereTo($request->to)->first();
            $bus = Bus::whereNumber($request->bus_no)->first();

            $booking = Booking::updateOrCreate([
                'route_id' => $route->id,
                'bus_id' => $bus->id,
                'agent_id' => Auth::user()->id,
                'psg_name' => $request->psg_name,
                'fare' => $request->fare,
                'dep_date' => $request->dep_date,
                'dep_time' => $request->dep_time,
                'seat_no' => $request->seat_no,
            ]);

            if ($booking) {
                return response()->json([
                    'status' => 'success',
                    'statusCode' => env('STATUS_CODE_PREFIX') . '200'
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

    function list(Request $request)
    {
        $bookings = Booking::when(isset($request->depDate) && isset($request->busId), fn($query) => $query->where('DATE(dep_date)', $request->dep_date)->whereBusId($request->busId))
            ->whereIn('route_id', [1])
            ->groupBy('dep_date')->orderBy('id', 'desc')->get();
        return BookingResource::collection($bookings)->resolve();
    }

    function busSchedules(Request $request) {
        $bus = Bus::find($request->busNo);
        $bookings = Booking::whereHas('bus', fn($bus) => $bus->whereNumber($request->busNo))
        ->where('dep_date', Carbon::parse($request->depDate)->format('Y-m-d'))
        ->groupBy('dep_time')
        ->orderBy('dep_time', 'ASC')
        ->get();
        return BookingResource::collection($bookings)->resolve();
    }

    function busPassengers(Request $request) {
        $bus = Bus::find($request->busNo);
        $bookings = Booking::whereHas('bus', fn($bus) => $bus->whereNumber($request->busNo))
        ->where('dep_date', Carbon::parse($request->depDate)->format('Y-m-d'))
        ->where('dep_time', $request->depTime)
        ->orderBy('id', 'DESC')
        ->get();
        return BookingResource::collection($bookings)->resolve();
    }
}
