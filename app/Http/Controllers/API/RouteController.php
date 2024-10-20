<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Bus;
use App\Models\Route;
use App\Models\BusRoute;
use Illuminate\Http\Request;
use App\Http\Resources\BusResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\FareResource;
use App\Http\Resources\RouteResource;
use App\Http\Resources\RouteBusesResource;
use App\Http\Resources\RouteScheduleResource;
use App\Http\Resources\TimeTableResource;
use Exception;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
     function list(Request $request)
     {
          $routes = Route::groupBy('from', 'to')->get();
          return RouteResource::collection($routes)->resolve();
     }

     function routeList(Request $request)
     {
          $routes = Route::groupBy('from')->get();
          return RouteResource::collection($routes)->resolve();
     }

     function routeDestinations(Request $request)
     {
          $routes = Route::where('from', $request->from)
               ->groupBy('to')->get();
          return RouteResource::collection($routes)->resolve();
     }

     public function availableBuses(Request $request)
     {
          $route = getRouteInstance($request->from, $request->to);
          return RouteBusesResource::collection($route->busRoutes)->resolve();
     }

     public function routeBuses(Request $request)
     {
          $route = getRouteInstance($request->from, $request->to);
          return RouteBusesResource::collection($route->busRoutes)->resolve();
     }

     public function routeSchedules(Request $request)
     {
          $route = getRouteInstance($request->from, $request->to);
          return RouteScheduleResource::collection($route->schedules)->resolve();
     }

     function createRoute(Request $request)
     {
          try {
               $fromRouteResposne = $this->routeCreateProcess($request, $request->from, $request->to);
               if ($request->type == 'two-way') {
                    $toRoute = $this->routeCreateProcess($request, $request->to, $request->from);
                    if ($fromRouteResposne['status'] == 'success') {
                         $response = $fromRouteResposne;
                    } else if ($toRoute['status'] == 'success') {
                         $response = $toRoute;
                    } else {
                         $response = $fromRouteResposne;
                    }
                    return response()->json($response);
               }
               return $fromRouteResposne;
          } catch (\Throwable $th) {
               return response()->json([
                    'status' => 'error',
                    'error' => $th->getMessage()
               ]);
          }
     }

     function routeCreateProcess(Request $request, $from, $to)
     {
          $route = getRouteInstance($request->from, $request->to);
          if (is_null($route)) {
               $route = Route::create([
                    'from' => $from,
                    'to' => $to,
                    'distance' => $request->distance,
                    'time' => $request->time,
               ]);
               if ($route) {
                    return [
                         'status' => 'success',
                         'route' => fullRoute($route)
                    ];
               } else {
                    return [
                         'status' => 'failed',
                         'message' => 'Failed to create a new route'
                    ];
               }
          } else {
               return [
                    'status' => 'exits',
                    'message' => 'Route: ' . fullRoute($route) . ' exists!'
               ];
          }
     }

     function getUnassignedBuses(Request $request)
     {

          $route = getRouteInstance($request->from, $request->to);
          $assignedBusIds = $route->busRoutes()->get()->pluck('bus_id');
          return BusResource::collection(Bus::whereNotIn('id', $assignedBusIds)->get())->resolve();
     }

     function assignBusToRoute(Request $request)
     {
          try {
               $bus = getBus($request->busNo);
               $route = getRouteInstance($request->from, $request->to);
               $busRoute = BusRoute::updateOrCreate([
                    'bus_id' => $bus->id,
                    'route_id' => $route->id,
               ]);
               if ($busRoute) {
                    return response()->json([
                         'status' => 'success',
                         'routeBuses' => $route->busRoutes,
                         'message' => $bus->number . ' Assigned to this route',
                    ]);
               } else {
                    return response()->json([
                         'status' => 'failed',
                         'message' => $bus->number . ' failed to be assigned on this route',
                    ]);
               }
          } catch (\Throwable $th) {
               return response()->json([
                    'status' => 'error',
                    'error' => $th->getMessage()
               ]);
          }
     }

     function routeTimetable(Request $request)
     {
          // return [$request->all()];
          $route = getRouteInstance($request->from, $request->to);
          $activeTimetable = $route->timetables()
               ->whereDate('dep_time', $request->depDate)
               ->whereIsActive(true)
               ->orderBy('dep_time', 'asc')
               // ->whereRaw("dep_time >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')" , Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i'))
               ->get();
          return TimeTableResource::collection($activeTimetable)->resolve();
     }

     function routeGroupedTimetable(Request $request)
     {
          $route = getRouteInstance($request->from, $request->to);
          $groupedDates = $route->timetables()
               ->select(DB::raw('DATE(dep_time) as depDate'))
               ->orderBy('depDate', 'desc')->groupBy('depDate')
               ->get()->pluck('depDate');
          $timetables = [];
          foreach ($groupedDates as $groupedDate) {
               $timetables[$groupedDate] = TimeTableResource::collection($route->timetables()->whereDate('dep_time', $groupedDate)->get())->resolve();
          }

          return response()->json([
               'dates' => $groupedDates,
               'timetables' => $timetables
          ]);
     }

     function routeFares(Request $request)
     {
          $fares = getRouteInstance($request->from, $request->to)->fares;
          return FareResource::collection($fares)->resolve();
     }

     function routeFareCreate(Request $request)
     {
          try {
               $route = getRouteInstance($request->from, $request->to);
               $fare = $route->fares()->updateOrCreate([
                    'route_id' => $route->id,
                    'bus_class_id' => $request->bus_class_id,
               ], [
                    'route_id' => $route->id,
                    'bus_class_id' => $request->bus_class_id,
                    'fare' => $request->fare
               ]);
               if ($fare) {
                    return response()->json([
                         'status' => 'success',
                         'fares' => FareResource::collection($route->fares)->resolve()
                    ]);
               }
               return response()->json([
                    'status' => 'failed',
               ]);
          } catch (Exception $e) {
               return response()->json([
                    'status' => 'failed',
                    'message' => 'Something went wrong!',
                    'error' => $e->getMessage(),
               ]);
          }
     }
}
