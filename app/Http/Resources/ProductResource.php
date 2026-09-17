<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'info' => $this->info,
            'info_ar' => $this->info_ar,
            'image_url' => $this->image_url,
        ];
    }
}
