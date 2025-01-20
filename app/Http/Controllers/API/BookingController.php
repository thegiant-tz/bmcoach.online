<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BLSMS;
use App\Models\Bus;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AgentTimetableCollectionResource;
use App\Http\Resources\BoardingPointResource;
use App\Http\Resources\BookingResource;
use App\Http\Resources\MyBookingsResource;
use App\Models\BoardingPoint;
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
            if ($request->userRole == 'agent' || is_null($request->userRole)) {
                $timetable = Timetable::find($request->timetableId);
                if (is_null($timetable->bookings()->whereSeatNo($request->seat_no)->first())) {
                    $booking = Booking::updateOrCreate([
                        'timetable_id' => $timetable->id,
                        'agent_id' => $agentId = ($request->userRole == 'agent' || is_null($request->userRole)) ?  Auth::user()->id : defaultAgentId(),
                        'status' => 'Processing'
                    ], [
                        'route_id' => $timetable->route->id,
                        'timetable_id' => $timetable->id,
                        'boarding_point_id' => $request->boardingPointId,
                        'dropping_point_id' => null,
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
                } else {
                    return response()->json([
                        'status' => 'seat already taken',
                        'statusCode' => env('STATUS_CODE_PREFIX') . '401'
                    ], 400);
                }
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
                ->when(isset($request->username), fn($query) => $query->whereAgentId(userFromUsername($request->username)->id))
                ->groupBy('agent_id')->get();
            return AgentTimetableCollectionResource::collection($bookings)->resolve();
        } catch (\Throwable $th) {
            return errorResponse($th);
        }
    }

    function listAll(Request $request)
    {
        $bookings = Booking::when(isset($request->status), fn($booking) => $booking->whereStatus($request->status))
            ->when(isset($request->bookingId), fn($query) => $query->whereId(codeIdToId($request->bookingId, false)))
            ->when(isset($request->bookingDate), fn($query) => $query->whereDate('created_at', $request->bookingDate))
            ->when(isset($request->departureDate), fn($query) => $query->whereDate('dep_date', $request->departureDate))
            /** agentName <==> agent Id */
            ->when(isset($request->agentName), fn($query) => $query->whereAgentId($request->agentName))
            /** agentCode <==> agent Id */
            ->when(isset($request->agentCode), fn($query) => $query->whereAgentId($request->agentCode))
            ->when(isset($request->origin), fn($query) => $query->whereHas('timetable.route', fn($route) => $route->where('from', $request->origin)))
            ->when(isset($request->destination), fn($query) => $query->whereHas('timetable.route', fn($route) => $route->where('to', $request->destination)))
            ->when(isset($request->busNumber), fn($query) => $query->whereHas('timetable', fn($timetable) => $timetable->whereBusId($request->busNumber)))
            ->orderby('id', 'DESC');
            if ($request->isPaginate) {
                $bookings = $bookings->paginate(57);
            } else {
                $bookings = $bookings->get();
            }
        return BookingResource::collection($bookings);
    }

    function list(Request $request)
    {
        try {
            $bookings = Booking::whereDate('dep_date', $request->depDate)
                ->when(isset($request->username), fn($query) => $query->whereAgentId(userFromUsername($request->username)->id))
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
                        ->when(isset($request->username), fn($query) => $query->whereAgentId(userFromUsername($request->username)->id))
                        ->groupBy('bus_id')
                        ->get()
                );
            }

            return response([
                'status' => 'success',
                'routes' => $bookings->pluck('routes'),
                'bookings' => $myBookings,
                'revenue' => $this->agentTotalCollection($request)
            ]);
        } catch (\Throwable $th) {
            return response([
                'status' => 'failed',
            ]);
        }
    }

    function agentTotalCollection(Request $request)
    {
        $bookings = Booking::whereDate('dep_date', $request->depDate)
            ->select(DB::raw('timetable_id, sum(fare) as total_collection, count(*) as total_passengers'))
            ->when(isAgent(), fn($query) => $query->whereAgentId(authUser()->id))
            ->when(isset($request->username), fn($query) => $query->whereAgentId(userFromUsername($request->username)->id))
            ->first();
        return MyBookingsResource::make($bookings)->resolve();
    }

    function boardingPointList()
    {
        $points = BoardingPoint::orderBy('point', 'ASC')->get();
        return BoardingPointResource::collection($points)->all();
    }
}
