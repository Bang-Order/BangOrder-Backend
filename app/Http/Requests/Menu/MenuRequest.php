<?php

namespace App\Http\Requests\Menu;

use App\Menu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MenuRequest extends FormRequest
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
                return $this->user()->can('create', [Menu::class, $restaurant_id]);
            default:
                return $this->user()->can('update', [$this->menu, $restaurant_id]);
        }
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
            'menu_category_id' => ['integer', 'exists:App\MenuCategory,id'],
            'description' => ['string'],
            'price' => ['sometimes', 'required', 'integer', 'gte:100'],
            'image' => ['mimes:jpg,jpeg,png', 'file', 'max:1024'],
            'is_available' => ['boolean'],
            'is_recommended' => ['boolean'],
        ];
    }
}
