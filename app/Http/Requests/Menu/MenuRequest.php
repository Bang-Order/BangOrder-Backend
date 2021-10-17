<?php

namespace App\Http\Requests\Menu;

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
        $auth_id = $this->user()->id;
        if ($auth_id == $this->restaurant->id) {
            if (in_array($this->method(), array('PUT', 'PATCH'))) {
                if ($auth_id == $this->menu->restaurant_id) {
                    return true;
                }
                return false;
            }
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
