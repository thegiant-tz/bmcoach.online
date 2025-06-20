<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Bus;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BusResource;
use App\Models\BusClass;
use App\Models\BusLayout;

class BusController extends Controller
{
    function busSchedules(Request $request)
    {
        $bus = Bus::find($request->busNo);
        $bookings = Booking::whereHas('bus', fn($bus) => $bus->whereNumber($request->busNo))
            ->where('dep_date', Carbon::parse($request->depDate)->format('Y-m-d'))
            ->groupBy('dep_time')
            ->orderBy('dep_time', 'ASC')
            ->get();
        return BookingResource::collection($bookings)->resolve();
    }

    function busPassengers(Request $request)
    {
        $bus = Bus::find($request->busNo);
        $bookings = Booking::whereHas('bus', fn($bus) => $bus->whereNumber($request->busNo))
            ->where('dep_date', Carbon::parse($request->depDate)->format('Y-m-d'))
            ->where('dep_time', $request->depTime)
            ->orderBy('id', 'DESC')
            ->get();
        return BookingResource::collection($bookings)->resolve();
    }

    function createBus(Request $request)
    {
        try {
            $bus = Bus::whereNumber($request->number)->first();
            if (is_null($bus)) {
                $bus = Bus::updateOrCreate([
                    'number' => $request->number
                ], [
                    'number' => $request->number,
                    'capacity' => $request->capacity,
                    'model' => $request->model,
                    'status' => $request->status ?? 'available',
                    'bus_layout_id' => $request->bus_layout_id,
                    'bus_class_id' => $request->bus_class_id,
                ]);
                if ($bus) {
                    return response()->json([
                        'status' => 'success',
                        'busno' => $bus->number
                    ]);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Failed to create a new bus'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'exits',
                    'message' => 'Bus number: ' . $bus->number . ' exists!'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'error' => $th->getMessage()
            ]);
        }
    }

    function list(Request $request)
    {
        try {
            $perPage = $request->perPage ?? 20;
            $busQuery = Bus::orderBy('id', 'DESC')->orderBy('capacity', 'DESC');
            $buses = (bool) $request->isPaginate ? $busQuery->paginate($perPage) : $busQuery->get();
            
            $busResource = BusResource::collection($buses);
            return (bool) $request->isPaginate ? $busResource : $busResource->resolve();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'error' => $th->getMessage()
            ]);
        }
    }

    function busLayouts(Request $request) {
        return BusLayout::all();
    }

    function busClasses(Request $request) {
        return BusClass::all();
    }
}
