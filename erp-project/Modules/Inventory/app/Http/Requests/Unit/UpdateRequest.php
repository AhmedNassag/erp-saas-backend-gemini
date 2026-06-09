<?php

namespace Modules\Inventory\Http\Requests\Unit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'name'           => 'required|string|unique:tenant.units,name,'.$this->id.',id,deleted_at,NULL',
            'code'           => 'required|unique:tenant.units,code,'.$this->id.',id,deleted_at,NULL',
            'shortName'      => 'required|string|max:192',
            'base_unit'      => 'nullable|exists:tenant.units,id',
            'operator'       => 'nullable|in:*,/',
            'operator_value' => 'nullable|numeric',
        ];

        return $roles;
    }

    public function messages()
    {
        return [
            'name.required'      => trans('validation.required'),
            'name.unique'        => trans('validation.unique'),
            'code.required'      => trans('validation.required'),
            'code.unique'        => trans('validation.unique'),
            'shortName.required' => trans('validation.required'),
            'base_unit.exists'   => trans('validation.exists'),
            'operator.in'        => trans('validation.in'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
