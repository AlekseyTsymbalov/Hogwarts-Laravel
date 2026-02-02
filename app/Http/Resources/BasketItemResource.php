<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasketItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $base = (string)$this->product->price;
        $final = $base;

        return [
            'item_id'  => $this->id,
            'quantity' => (int) $this->quantity,
            'product'  => [
                'id'    => $this->product->id,
                'name'  => $this->product->name,
                'image' => $this->product->image,
            ],
            'price' => [
                'base'  => $base,
                'final' => $final,
            ],
        ];
    }
}
