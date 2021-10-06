<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\OrderItem\OrderItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $totalPrice = 0;
        for ($x = 0; $x < $this->orderItems->count(); $x++) {
            $totalPrice += $this->orderItems[$x]->menu->price * $this->orderItems[$x]->quantity;
        }

        return [
            'order-id' => $this->id,
            'table-id' => $this->restaurant_table_id,
            'order-status' => $this->order_status,
            'order-items' => OrderItemResource::collection($this->orderItems),
            'total-price' => "Rp" . number_format($totalPrice, 0, ',', '.')
        ];
    }
}
