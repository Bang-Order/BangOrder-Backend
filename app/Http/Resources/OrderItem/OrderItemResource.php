<?php

namespace App\Http\Resources\OrderItem;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->menu_name,
            'quantity' => $this->quantity,
            'price' => number_format($this->sum_price, 0, ',', '.'),
            'notes' => $this->notes,
        ];
    }
}
