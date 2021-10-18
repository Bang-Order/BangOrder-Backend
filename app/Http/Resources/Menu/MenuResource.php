<?php

namespace App\Http\Resources\Menu;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'name' => $this->name,
            'menu_category_id' => $this->menu_category_id,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image,
            'is_available' => $this->is_available,
            'is_recommended' => $this->is_recommended
        ];
    }
}
