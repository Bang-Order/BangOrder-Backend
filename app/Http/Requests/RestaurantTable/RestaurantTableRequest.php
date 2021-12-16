<?php

namespace App\Http\Requests\RestaurantTable;

use App\RestaurantTable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RestaurantTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $restaurant_id = $this->restaurant->id;
        switch ($this->method()) {
            case 'POST':
                return $this->user()->can('create', [RestaurantTable::class, $restaurant_id]);
            default:
                return $this->user()->can('update', [$this->table, $restaurant_id]);
        }
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response()->json(['message' => 'Anda tidak diizinkan untuk melakukan aksi ini'], 401));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'table_number' => ['required', 'string', 'max:5']
        ];
    }
}
