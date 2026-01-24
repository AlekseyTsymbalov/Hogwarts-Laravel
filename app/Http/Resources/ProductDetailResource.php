<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->resource->id,
            'name'        => $this->resource->name,
            'price'       => $this->resource->price,
            'description' => $this->resource->description,
            'image'       => $this->resource->image,
            'section_id'  => $this->resource->section_id,
        ];
    }
}
