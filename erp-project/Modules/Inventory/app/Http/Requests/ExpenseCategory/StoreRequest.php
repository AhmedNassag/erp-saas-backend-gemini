<?php

namespace Modules\Inventory\Http\Requests\ExpenseCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'name'        => 'required|string|unique:tenant.expense_categories,name,NULL,id,deleted_at,NULL',
            'description' => 'nullable|string',
        ];

        return $roles;
    }

    public function messages()
    {
        $messages = [
            'name.required'      => trans('validation.required'),
            'name.string'        => trans('validation.string'),
            'name.unique'        => trans('validation.unique'),
            'description.string' => trans('validation.string'),
        ];

        return $messages;
    }

    public function authorize(): bool
    {
        return true;
    }
}
