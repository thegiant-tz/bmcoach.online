<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RouteBusesResource;
use App\Http\Resources\RouteResource;
use App\Http\Resources\RouteScheduleResource;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
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
          $route = Route::where('from', $request->from)
               ->where('to', $request->to)->first();
          return RouteBusesResource::collection($route->busRoutes)->resolve();
     }

     public function routeSchedules(Request $request)
     {
          $route = Route::where('from', $request->from)
               ->where('to', $request->to)->first();
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
          $route = Route::whereFrom($from = strtoupper($from))
               ->whereTo($to = strtoupper($to))
               ->first();
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
                    'message' => 'Route: ' . fullRoute($route). ' exists!'
               ];
          }
     }
}
