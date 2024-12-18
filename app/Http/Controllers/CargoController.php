<?php

namespace App\Http\Controllers;

use App\Http\Resources\CargoResource;
use App\Models\Cargo;
use App\Models\CargoTracker;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function store(Request $request)
    {
        $cargo = Cargo::updateOrCreate([
            'route_id' => getRouteInstance($request->from, $request->to)->id,
            'user_id' => authUser()->id,
            'sender_name' => $request->sender_name,
            'sender_phone' => $request->sender_phone,
            'sender_email' => $request->sender_email,
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'item_name' => $request->item_name,
            'item_value' => $request->item_value,
            'weight' => $request->weight,
            'size' => $request->size,
            'amount' => $request->amount,
            'dep_date' => $request->dep_date,
        ]);

        CargoTracker::updateOrCreate([
            'respondent' => authUser()->id,
            'cargo_id' => $cargo->id 
        ]);

        return !is_null($cargo) ? response(['message' => 'success', 'cargo' => CargoResource::make($cargo)->resolve()]) : response(['message' => 'failed']);
    }

    function list(Request $request)
    {
        $cargos = Cargo::when(isset($request->getMyCargos), fn($cargo) => $cargo->whereUserId(authUser()->id)->latest()->take($request->limit ?? 10))
            ->when(isset($request->status), fn($cargo) => $cargo->whereHas('cargoTrackers', fn($cargoTracker) => $cargoTracker->whereStatus($request->status)))
            ->when(isset($request->bookingId), fn($query) => $query->whereId(codeIdToId($request->bookingId)))
            ->when(isset($request->bookingDate), fn($query) => $query->whereDate('created_at', $request->bookingDate))
            ->when(isset($request->departureDate), fn($query) => $query->whereDate('dep_date', $request->departureDate))
            /** agentName <==> agent Id */ 
            ->when(isset($request->agentName), fn($query) => $query->whereUserId($request->agentName))
            /** agentCode <==> agent Id */ 
            ->when(isset($request->agentCode), fn($query) => $query->whereUserId($request->agentCode))
            ->when(isset($request->origin), fn($query) => $query->whereHas('route', fn($route) => $route->where('from', $request->origin)))
            ->when(isset($request->destination), fn($query) => $query->whereHas('route', fn($route) => $route->where('to', $request->destination)))
            ->when(isset($request->busNumber), fn($query) => $query->whereHas('cargoTrackers', fn($cargoTrackers) => $cargoTrackers->whereBusId($request->busNumber)))
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $cargoResource = CargoResource::collection($cargos);
        return isWebAPI() ? $cargoResource : $cargoResource->resolve();
    }

    function boarding(Request $request)
    {
        if (aes_decrypt($request->codeId)) {
            $cargoId = codeIdToId(aes_decrypt($request->codeId));
            $cargoTracker = CargoTracker::whereCargoId($cargoId)->whereStatus('In Transit')->first();

            if ($request->isOnboardingPage == 'onboarding') {
                return $this->onboard($request, $cargoId, $cargoTracker);
            }
            return $this->offboard($request, $cargoId, $cargoTracker);
        }
        return [
            'message' => 'invalid qrcode',
            'status' => 'Invalid QrCode Detected',
        ];
    }

    private function offboard(Request $request, int $cargoId, CargoTracker $cargoTracker = null)
    {
        // if (!is_null($cargoTracker)) {
        $isDeliveredCargo = !is_null(CargoTracker::whereCargoId($cargoId)->whereStatus('Delivered')->first());
        if (!$isDeliveredCargo) {
            $cargoTracker = CargoTracker::updateOrCreate([
                'cargo_id' => $cargoId,
                'respondent' => authUser()->id,
                'status' => 'Delivered',
                'bus_id' => $cargoTracker->bus_id ?? null,
            ]);
            if ($cargoTracker) {
                return [
                    'tracker' => $cargoTracker,
                    'message' => 'success'
                ];
            }
            return [
                'message' => 'failed'
            ];
        }
        return [
            'message' => 'exists'
        ];
        // }

    }

    private function onboard(Request $request, int $cargoId, CargoTracker $cargoTracker = null)
    {
        if (is_null($cargoTracker)) {
            $cargoTracker = CargoTracker::updateOrCreate([
                'cargo_id' => $cargoId,
                'respondent' => authUser()->id,
                'status' => 'In Transit',
                'bus_id' => getBus($request->busNo)->id,
            ]);

            if ($cargoTracker) {
                return [
                    'tracker' => $cargoTracker,
                    'message' => 'success'
                ];
            }
            return [
                'message' => 'failed'
            ];
        }
        return [
            'message' => 'exists'
        ];
    }
}
