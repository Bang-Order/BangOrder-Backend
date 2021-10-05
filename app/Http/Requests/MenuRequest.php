<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'menu_category_id' => ['integer', 'exists:App\MenuCategory,id'],
            'description' => ['string'],
            'price' => ['required', 'integer', 'gte:100'],
            'image' => ['active_url'], //change it to image or active_url later
            'is_available' => ['boolean'],
            'is_recommended' => ['boolean'],
        ];
    }
}
