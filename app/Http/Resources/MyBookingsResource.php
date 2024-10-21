<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyBookingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_collection' => $this->total_collection,
            'total_passengers' => $this->total_passengers,
            'timetable' => TimeTableResource::make($this->timetable),
        ];
    }
}
