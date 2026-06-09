<?php

namespace Modules\Inventory\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'companyName'  => 'required|string',
            'companyPhone' => 'required|string',
            'companyAdress'=> 'required|string',
            'currency_id'  => 'required|exists:tenant.currencies,id',
            'client_id'    => 'required|exists:tenant.clients,id',
            'warehouse_id' => 'required|exists:tenant.warehouses,id',
            'image'    => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
            'images.*' => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
        ];

        return $roles;
    }

    public function messages()
    {
        return [
            'companyName.required'   => trans('validation.required'),
            'companyPhone.required'  => trans('validation.required'),
            'companyAdress.required' => trans('validation.required'),
            'currency_id.required'   => trans('validation.required'),
            'currency_id.exists'     => trans('validation.exists'),
            'client_id.required'     => trans('validation.required'),
            'client_id.exists'       => trans('validation.exists'),
            'warehouse_id.required'  => trans('validation.required'),
            'warehouse_id.exists'    => trans('validation.exists'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
