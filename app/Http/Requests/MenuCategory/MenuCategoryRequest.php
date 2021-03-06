<?php

namespace App\Http\Requests\MenuCategory;


use App\MenuCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class MenuCategoryRequest extends FormRequest
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
                return $this->user()->can('create', [MenuCategory::class, $restaurant_id]);
            default:
                return $this->user()->can('update', [$this->menu_category, $restaurant_id]);
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
            'name' => ['required', 'string']
        ];
    }
}
