<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'restaurant_table_id' => ['required', 'integer', 'exists:App\RestaurantTable,id'],
            'total_price' => ['required', 'integer', 'gte:100'],
            'order_items' => ['required', 'array'],
        ];
    }
}
