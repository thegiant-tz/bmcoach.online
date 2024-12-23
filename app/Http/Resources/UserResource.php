<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role->name, 
            'phone' => $this->phone,
            'pay_type' => $this->pay_type,
            'is_active' => $this->status,
            'currency' => $this->currency,
            'tin' => $this->tin,
            'reg_no' => $this->reg_no,
            'agent_class' => $this->agent_class,
            'ward' => $this->boardingPoint->point ?? '',
        ];
    }
}
