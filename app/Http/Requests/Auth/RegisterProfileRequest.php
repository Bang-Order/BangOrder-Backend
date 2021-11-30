<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterProfileRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'image' => ['mimes:jpg,jpeg,png', 'file', 'max:1024'],
//            'table_amount' => ['required', 'integer', 'gt:0'],
            'owner_name' => ['required', 'string'],
            'telephone_number' => ['required', 'phone:ID'],
            'bank_name' => ['required'],
            'account_holder_name' => ['required', 'string'],
            'account_number' => ['required', 'numeric']
        ];
    }
}
