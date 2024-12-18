<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusResource extends JsonResource
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
            'number' => $this->number, 
            'capacity' => $this->capacity,
            'model' => $this->model,
            'status' => $this->status,
            'layout' => [
                'label' => $this->layout->label,
                'name' => $this->layout->name,
            ],
            'class' => $this->busClass->name,
        ];
    }
}
