<?php

namespace Modules\Inventory\Http\Requests\ExpenseCategory;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => trans('validation.required'),
            'status.boolean'  => trans('validation.boolean'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
