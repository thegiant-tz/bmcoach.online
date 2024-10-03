<?php

namespace App\Http\Resources;

use App\Models\RouteSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'from' => $this->from, 
            'to' => $this->to, 
            'distance' => $this->distance,
            'time' => $this->time,
            'schedules' => RouteScheduleResource::collection($this->schedules)
        ];
    }
}
