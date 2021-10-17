<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //dd($this);
        return [
            'message' => 'Login Success',
            'data' => [
                'id' => $this['restaurant']->id,
                'name' => $this['restaurant']->name,
                'access_token' => $this['token'],
                'token_type' => 'Bearer',
            ]
        ];
    }
}
