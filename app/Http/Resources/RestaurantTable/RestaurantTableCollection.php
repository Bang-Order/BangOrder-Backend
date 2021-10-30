<?php

namespace App\Http\Resources\RestaurantTable;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RestaurantTableCollection extends ResourceCollection
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
            'data' => RestaurantTableResource::collection($this->collection),
            'meta' => [
                'data_length' => $this->collection->count()
            ]
        ];
    }
}
