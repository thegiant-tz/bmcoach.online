<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CargoTrackerResource extends JsonResource
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
            'respondent' => UserResource::make($this->agent), 
            'bus' => BusResource::make($this->bus),
            // 'cargo' => CargoResource::make($this->cargo), 
            'status' => $this->status, 
            'createdAt' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ];
    }
}
