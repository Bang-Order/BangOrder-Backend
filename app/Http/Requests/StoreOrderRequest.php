<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'restaurant_table_id' => ['required', 'integer', 'exists:App\RestaurantTable,id'],
            'total_price' => ['required', 'integer', 'gte:100'],
            'order_items' => ['required', 'array'],
        ];
    }
}
