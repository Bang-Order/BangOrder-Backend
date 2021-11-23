<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class RestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('restaurant-auth', $this->restaurant);
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response()->json(['message' => 'This action is unauthorized.'], 401));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'email'],
            'address' => ['sometimes', 'required', 'string'],
            'image' => ['mimes:jpg,jpeg,png', 'file', 'max:1024'],
            'owner_name' => ['sometimes', 'required', 'string'],
            'telephone_number' => ['sometimes', 'required', 'phone:ID']
        ];
    }
}
