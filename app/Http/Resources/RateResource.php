<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'code' => $this->code,
            'rate' => (float) $this->rate,
            'currency' => $this->currency,
            'is_active' => $this->is_active,
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
