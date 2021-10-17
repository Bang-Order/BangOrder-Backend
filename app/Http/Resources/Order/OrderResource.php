<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\OrderItem\OrderItemCollection;
use App\Http\Resources\OrderItem\OrderItemResource;
use App\OrderItem;
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
        return [
            'id' => $this->id,
            'table_id' => $this->restaurant_table_id,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'total_price' => number_format($this->total_price, 0, ',', '.'),
            'created_at' => $this->created_at
        ];
    }
}
