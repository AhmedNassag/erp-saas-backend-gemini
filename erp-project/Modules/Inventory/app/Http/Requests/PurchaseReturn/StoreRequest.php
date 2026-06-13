<?php

namespace Modules\Inventory\Http\Requests\PurchaseReturn;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'provider_id'                  => 'required|exists:tenant.providers,id',
            'warehouse_id'                 => 'required|exists:tenant.warehouses,id',
            'date'                         => 'required|date',
            'status'                       => 'required|string',
            'tax_rate'                     => 'nullable|numeric|min:0',
            'TaxNet'                       => 'nullable|numeric|min:0',
            'discount'                     => 'nullable|numeric|min:0',
            'shipping'                     => 'nullable|numeric|min:0',
            'GrandTotal'                   => 'nullable|numeric|min:0',
            'notes'                        => 'nullable|string',
            'details'                      => 'required|array|min:1',
            'details.*.product_id'         => 'required|exists:tenant.products,id',
            'details.*.quantity'           => 'required|numeric|min:0.01',
            'details.*.cost'               => 'nullable|numeric|min:0',
            'details.*.purchase_unit_id'   => 'nullable|exists:tenant.units,id',
            'details.*.TaxNet'             => 'nullable|numeric',
            'details.*.tax_method'         => 'nullable|string|in:1,2',
            'details.*.discount'           => 'nullable|numeric|min:0',
            'details.*.discount_method'    => 'nullable|string|in:1,2',
            'details.*.total'              => 'nullable|numeric',
            'details.*.product_variant_id' => 'nullable|exists:tenant.product_variants,id',
        ];
    }

    public function messages()
    {
        return [
            'provider_id.required'         => trans('validation.required'),
            'warehouse_id.required'        => trans('validation.required'),
            'date.required'                => trans('validation.required'),
            'status.required'              => trans('validation.required'),
            'details.required'             => trans('validation.required'),
            'details.min'                  => trans('validation.min'),
            'details.*.product_id.required'=> trans('validation.required'),
            'details.*.quantity.required'  => trans('validation.required'),
            'details.*.quantity.min'       => trans('validation.min'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
