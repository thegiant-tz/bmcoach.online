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
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return CargoResource::collection($cargos);
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
