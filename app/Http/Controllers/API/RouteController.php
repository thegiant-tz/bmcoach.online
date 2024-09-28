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
}
