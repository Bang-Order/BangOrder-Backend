<?php

namespace App\Http\Resources\Restaurant;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            'email' => $this->email,
            'address' => $this->address,
            'image' => $this->image,
            'owner_name' => $this->owner_name,
            'telephone_number' => $this->telephone_number,
            'bank_name' => $this->bankAccount->bank_name,
            'account_holder_name' => $this->bankAccount->account_holder_name,
            'account_number' => $this->bankAccount->account_number,
        ];
    }
}
