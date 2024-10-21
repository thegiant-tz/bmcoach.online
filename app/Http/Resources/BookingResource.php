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
            'timetable' => TimeTableResource::make($this->timetable)
        ];
    }
}
