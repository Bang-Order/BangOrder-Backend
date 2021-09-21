<?php

namespace App\Http\Resources\MenuCategory;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuCategoryCollection extends ResourceCollection
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
            'data' => MenuCategoryResource::collection($this->collection),
            'meta' => [
                'data_length' => $this->collection->count()
            ]
        ];
    }
}
