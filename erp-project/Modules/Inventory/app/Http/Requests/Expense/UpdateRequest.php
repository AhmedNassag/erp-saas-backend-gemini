<?php

namespace Modules\Inventory\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'date'                => 'required|date',
            'details'             => 'required|string|max:192',
            'amount'              => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:tenant.expense_categories,id',
            'warehouse_id'        => 'required|exists:tenant.warehouses,id',
        ];
    }

    public function messages()
    {
        return [
            'date.required'                => trans('validation.required'),
            'date.date'                    => trans('validation.date'),
            'details.required'             => trans('validation.required'),
            'details.string'               => trans('validation.string'),
            'details.max'                  => trans('validation.max'),
            'amount.required'              => trans('validation.required'),
            'amount.numeric'               => trans('validation.numeric'),
            'amount.min'                   => trans('validation.min'),
            'expense_category_id.required' => trans('validation.exists'),
            'expense_category_id.exists'   => trans('validation.exists'),
            'warehouse_id.required'        => trans('validation.required'),
            'warehouse_id.exists'          => trans('validation.exists'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
