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
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'restaurant_name' => $this->restaurant->name,
            'restaurant_image' => $this->restaurant->image,
            'table_id' => $this->restaurant_table_id,
            'table_number' => $this->restaurantTable->table_number,
            'transaction_id' => $this->transaction_id,
            'invoice_url' => $this->invoice_url,
            'order_status' => $this->order_status,
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'total_price' => number_format($this->total_price, 0, ',', '.'),
            'created_at' => $this->created_at->format('d-m-Y H:i')
        ];
    }
}
