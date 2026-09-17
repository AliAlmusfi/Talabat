<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'report_id' => $this->id,
            'type' =>$this->type,
            'info' => $this->info,
            'user_id' => $this->user_id,
            'market_id' => $this->market_id
        ];
    }
}
