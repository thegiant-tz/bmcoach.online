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
            'psgPhone' => $this->psg_phone,
            'seatNo' => $this->seat_no,
            'ticketNo' => 'BM' . str_pad($this->id, 5, '0', STR_PAD_LEFT),
            'fare' => $this->fare,
            'agentName' => $this->agent->name,
            'agentCode' => $this->agent->username,
            'timetable' => TimeTableResource::make($this->timetable)
        ];
    }
}
