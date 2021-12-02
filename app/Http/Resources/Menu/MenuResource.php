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
        $menuCategory = $this->menuCategory ? $this->menuCategory->name : null;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category_id' => $this->menu_category_id,
            'menu_category' => $menuCategory,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image,
            'is_available' => $this->is_available,
            'is_recommended' => $this->is_recommended
        ];
    }
}
