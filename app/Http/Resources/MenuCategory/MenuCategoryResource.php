<?php

namespace App\Http\Resources\MenuCategory;

use App\Http\Resources\Menu\MenuResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuCategoryResource extends JsonResource
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
            // 'menu' => MenuResource::collection($this->menus)
        ];
    }
}
