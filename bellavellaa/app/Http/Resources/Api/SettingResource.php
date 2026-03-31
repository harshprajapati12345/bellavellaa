<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the setting into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'key'   => $this->key,
            'value' => $this->value,
            'group' => $this->group,
        ];
    }
}
