<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeTableResource extends JsonResource
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
            'bus' => BusResource::make($this->bus),
            'route' => RouteResource::make($this->route),
            'date' => ($date = Carbon::parse($this->dep_time))->format('d.m.Y'),
            'time' => $date->format('H:i'),
            'datetime' => $date->format('d.m.Y H:i'),
            'seats_left' => getBusSeatLeft($this->bus, $date->format('Y-m-d H:i:s'), $date->format('H:i:s')),
            'fare' => getFare($this->route, $this->bus),
            'is_active' => $this->is_active,
            'name' => $this->timetableName()
        ];
    }

    private function timetableName() {
        $busNo = str_replace(' ', '_', $this->bus->number);
        $route = substr($this->route->from, 0, 3) . '_' . substr($this->route->to, 0, 3);
        $date = Carbon::parse($this->dep_time)->format('d_m_Y_H_i');
        return $route . '_' . $busNo . '_'. $date;
    }
}
