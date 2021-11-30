<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\Restaurant\RestaurantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
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
            'id' => $this['restaurant']->id,
            'name' => $this['restaurant']->name,
            'email' => $this['restaurant']->email,
            'address' => $this['restaurant']->address,
            'image' => $this['restaurant']->image,
            'owner_name' => $this['restaurant']->owner_name,
            'telephone_number' => $this['restaurant']->telephone_number,
            'bank_name' => $this['restaurant']->bankAccount->bank_name,
            'account_holder_name' => $this['restaurant']->bankAccount->account_holder_name,
            'account_number' => $this['restaurant']->bankAccount->account_number,
            'access_token' => $this['token'],
            'token_type' => 'Bearer'
        ];
    }
}
