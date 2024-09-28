<?php

namespace App\Http\Resources;

use App\Models\Booking;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'psgName' => $this->psg_name,
            'seatNo' => $this->seat_no,
            'fare' => $this->fare,
            'agentName' => authUser()->id == $this->agent_id ? 'Me' : $this->agent->name,
            'depDate' => [
                'date' => ($date = Carbon::parse($this->dep_date))->format('d.m.Y'),
                'dayName' => $date->isToday() ? 'Today' : ($date->isYesterday() ? 'Yesterday' : $date->format('l')),
            ],
            'depTime' => $this->dep_time,
            'buses' => BookingBusResource::collection(Booking::whereIn('route_id', [1])->where('dep_date', DATE($this->dep_date))->groupBy('bus_id')->get())->resolve(),
            'route' => RouteResource::make($this->route)->resolve()
        ];
    }
}
