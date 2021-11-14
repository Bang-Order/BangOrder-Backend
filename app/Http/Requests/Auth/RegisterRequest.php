<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'unique:App\Restaurant,email'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'string', 'min:8', 'same:password'],
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'image' => ['mimes:jpg,jpeg,png', 'file', 'max:1024'],
//            'table_amount' => ['required', 'integer', 'gt:0'],
            'owner_name' => ['required', 'string'],
            'telephone_number' => ['required', 'phone:ID']
        ];
    }
}
