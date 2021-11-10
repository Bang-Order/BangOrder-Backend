<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $auth_id = $this->user()->id;
        if ($auth_id == $this->restaurant->id) {
            return true;
        }
        return false;
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
            'telephone_number' => ['sometimes', 'required', 'numeric']
        ];
    }
}
