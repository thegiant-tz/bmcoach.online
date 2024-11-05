<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentTimetableCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'agentId' => $this->agent->id,
            'agentName' => $this->agent->name,
            'collection' => agentTimetableCollection($this->timetable_id, $this->agent_id),
            'total_passengers' => agentTimetableTickets($this->timetable_id, $this->agent_id),
        ];
    }
}
