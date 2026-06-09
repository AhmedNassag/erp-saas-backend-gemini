<?php

namespace Modules\Inventory\Http\Requests\Unit;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'status' => 'required|in:0,1',
        ];

        return $roles;
    }

    public function messages()
    {
        return [
            'status.required' => trans('validation.required'),
            'status.in'       => trans('validation.in'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
