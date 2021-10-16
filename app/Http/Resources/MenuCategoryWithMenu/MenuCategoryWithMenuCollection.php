<?php

namespace App\Http\Resources\MenuCategoryWithMenu;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuCategoryWithMenuCollection extends ResourceCollection
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
            'data' => MenuCategoryWithMenuResource::collection($this->collection),
            'meta' => [
                'data_length' => $this->collection->count()
            ]
        ];
    }
}
