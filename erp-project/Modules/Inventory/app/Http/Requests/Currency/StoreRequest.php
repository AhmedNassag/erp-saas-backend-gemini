<?php

namespace Modules\Inventory\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'name'   => 'required|string|unique:tenant.currencies,name,NULL,id,deleted_at,NULL',
            'code'   => 'required|unique:tenant.currencies,code,NULL,id,deleted_at,NULL',
            'symbol' => 'required|string|max:192',
        ];

        return $roles;
    }

    public function messages()
    {
        $messages = [
            'name.required'   => trans('validation.required'),
            'name.string'     => trans('validation.string'),
            'name.unique'     => trans('validation.unique'),
            'code.required'   => trans('validation.required'),
            'code.unique'     => trans('validation.unique'),
            'symbol.required' => trans('validation.required'),
            'symbol.max'      => trans('validation.max'),
        ];

        return $messages;
    }

    public function authorize(): bool
    {
        return true;
    }
}
