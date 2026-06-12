<?php

namespace Modules\Inventory\Http\Requests\Adjustment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'warehouse_id'                 => 'required|exists:tenant.warehouses,id',
            'date'                         => 'required|date',
            'notes'                        => 'nullable|string',
            'details'                      => 'required|array|min:1',
            'details.*.product_id'         => 'required|exists:tenant.products,id',
            'details.*.quantity'           => 'required|numeric|min:0.01',
            'details.*.type'               => 'required|in:add,sub',
            'details.*.product_variant_id' => 'nullable|exists:tenant.product_variants,id',
        ];
    }

    public function messages()
    {
        return [
            'warehouse_id.required'         => trans('validation.required'),
            'warehouse_id.exists'           => trans('validation.exists'),
            'date.required'                 => trans('validation.required'),
            'details.required'              => trans('validation.required'),
            'details.min'                   => trans('validation.min'),
            'details.*.product_id.required' => trans('validation.required'),
            'details.*.quantity.required'   => trans('validation.required'),
            'details.*.quantity.min'        => trans('validation.min'),
            'details.*.type.required'       => trans('validation.required'),
            'details.*.type.in'             => trans('validation.in'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
