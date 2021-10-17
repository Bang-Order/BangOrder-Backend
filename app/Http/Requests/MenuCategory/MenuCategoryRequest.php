<?php

namespace App\Http\Requests\MenuCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MenuCategoryRequest extends FormRequest
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
                if ($auth_id == $this->menu_category->restaurant_id) {
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
            'name' => ['required', 'string']
        ];
    }
}
