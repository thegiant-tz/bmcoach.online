<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BLSMS;
use App\Models\Bus;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AgentTimetableCollectionResource;
use App\Http\Resources\MyBookingsResource;
use App\Models\Timetable;
use App\Services\SMSService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                'agent_id' => $agentId = ($request->userRole == 'agent' || is_null($request->userRole)) ?  Auth::user()->id : defaultAgentId(),
                'status' => 'Processing'
            ], [
                'route_id' => $timetable->route->id,
                'timetable_id' => $timetable->id,
                'boarding_point_id' => $request->boardingPointId,
                'dropping_point_id' => $request->droppingPointId,
                'bus_id' => $timetable->bus->id,
                'agent_id' => $agentId,
                'psg_name' => $request->psg_name ?? null,
                'psg_phone' => $request->psg_phone ?? null,
                'fare' => $request->fare,
                'dep_date' => $timetable->dep_time,
                'dep_time' => Carbon::parse($timetable->dep_time)->format('H:i:s'),
                'seat_no' => $request->seat_no,
                'status' => $request->status ?? 'Processing'
            ]);

            if ($booking) {
                $ticketNo = 'BM' . str_pad($booking->id, 5, '0', STR_PAD_LEFT);
                BLSMS::_sendMessageBLSM(
                    message: SMSService::bookingSMS($booking, $ticketNo),
                    recipient: $booking->psg_phone
                );

                return response()->json([
                    'status' => 'success',
                    'statusCode' => env('STATUS_CODE_PREFIX') . '200',
                    'booking' => $booking,
                    'ticketNo' => $ticketNo
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
            return errorResponse($th);
        }
    }

    function agentTimetableCollection(Request $request)
    {
        try {
            $bookings = Booking::whereTimetableId($request->timetableId)
                ->groupBy('agent_id')->get();
            return AgentTimetableCollectionResource::collection($bookings)->resolve();
        } catch (\Throwable $th) {
            return errorResponse($th);
        }
    }

    function list(Request $request)
    {
        try {
            $bookings = Booking::whereDate('dep_date', $request->depDate)
                ->groupBy('route_id')->orderBy('id', 'desc')->get()->map(function ($booking) {
                    $booking->routes = $booking->route->from . ' - ' . $booking->route->to;
                    return $booking;
                });
            $myBookings = [];
            foreach ($bookings as $booking) {
                $route = $booking->route;
                $myBookings[$route->from . ' - ' . $route->to] = MyBookingsResource::collection(
                    Booking::whereDate('dep_date', $request->depDate)->whereRouteId($route->id)
                        ->select(DB::raw('timetable_id, sum(fare) as total_collection, count(*) as total_passengers'))
                        ->when(isAgent(), fn($query) => $query->whereAgentId(authUser()->id))
                        ->groupBy('bus_id')
                        ->get()
                );
            }

            return response([
                'status' => 'success',
                'routes' => $bookings->pluck('routes'),
                'bookings' => $myBookings
            ]);
        } catch (\Throwable $th) {
            return response([
                'status' => 'failed',
            ]);
        }
    }
}
