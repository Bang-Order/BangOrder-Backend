<?php

namespace App\Http\Resources\RestaurantTable;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantTableResource extends JsonResource
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
            'table_number' => $this->table_number,
            'link' => $this->link
        ];
    }
}
