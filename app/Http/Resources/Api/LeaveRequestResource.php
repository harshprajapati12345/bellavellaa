<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
{
    /**
     * Transform the leave request into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'professional'  => new ProfessionalResource($this->whenLoaded('professional')),
            'approver'      => $this->whenLoaded('approver', function() {
                return [
                    'id'   => $this->approver->id,
                    'name' => $this->approver->name,
                ];
            }),
            'leave_type'    => $this->leave_type,
            'reason'        => $this->reason,
            'start_date'    => $this->start_date?->format('Y-m-d'),
            'end_date'      => $this->end_date?->format('Y-m-d'),
            'status'        => $this->status,
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
