<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CargoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codeId' => aes_encrypt('BMC' . str_pad($this->id, 5, '0', STR_PAD_LEFT)),
            'agent' => UserResource::make($this->agent),
            'sender' => [
                'name' => $this->sender_name,
                'phone' => $this->sender_phone,
                'email' => $this->sender_email,
            ], 
            'receiver' => [
                'name' => $this->receiver_name,
                'phone' => $this->receiver_phone,
            ], 
            'item' => [
                'name' => $this->item_name,
                'value' => (double) $this->item_value,
                'weight' => (double) $this->weight,
                'size' => (double) $this->size,
                'amount' => (double) $this->amount,
            ],
            'shippingDate' => Carbon::parse($this->dep_date)->format('d/m/Y'),
            'bookingDate' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'route' => RouteResource::make($this->route),
            'bus' => BusResource::make($this->bus),
            'cargoTrackers' => CargoTrackerResource::collection($this->cargoTrackers),
            'status' => $this->latestCargoTracker->status ?? 'Pending'
        ];
    }
}
