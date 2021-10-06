<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Menu\MenuResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => OrderResource::collection($this->collection),
            'meta' => [
                'data_length' => $this->collection->count()
            ]
        ];
    }
}
