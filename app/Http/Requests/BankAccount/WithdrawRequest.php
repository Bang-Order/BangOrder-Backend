<?php

namespace App\Http\Requests\BankAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class WithdrawRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('restaurant-auth', $this->restaurant);
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
            'amount' => ['required', 'integer', 'gte:10000']
        ];
    }
}
