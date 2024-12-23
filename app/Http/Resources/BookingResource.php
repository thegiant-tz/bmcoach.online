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
        $names = explode(' ', $this->psg_name);
        $name = $names[0] . (!isset($names[1]) ? '' : ' ' . str_split($names[1])[0]);

        $names = explode(' ', $this->agent->name);
        $agentNamePdf = $names[0];
        return [

            'psgName' => $this->psg_name,
            'psgNamePdf' => $name,
            'psgPhone' => $this->psg_phone,
            'seatNo' => $this->seat_no,
            'ticketNo' => 'BM' . str_pad($this->id, 5, '0', STR_PAD_LEFT),
            'fare' => $this->fare,
            'agentName' => $this->agent->name,
            'agentNamePdf' => $agentNamePdf,
            'agentCode' => $this->agent->username,
            'timetable' => TimeTableResource::make($this->timetable),
            'bookedAt' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'depDate' => Carbon::parse($this->dep_date)->format('d/m/Y'),
            'boardingPoint' => $this->boardingPoint->point,
            'droppingPoint' => $this->droppingPoint->point,
            'status' => $this->status,
        ];
    }
}
