<?php

namespace Modules\Inventory\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'code'             => 'required|string|unique:tenant.products,code,' . $this->id . ',id,deleted_at,NULL',
            'Type_barcode'     => 'required|string',
            'name'             => 'required|string',
            'cost'             => 'required|numeric',
            'price'            => 'required|numeric',
            'category_id'      => 'required|exists:tenant.categories,id',
            'brand_id'         => 'nullable|exists:tenant.brands,id',
            'unit_id'          => 'required|exists:tenant.units,id',
            'unit_sale_id'     => 'nullable|exists:tenant.units,id',
            'unit_purchase_id' => 'nullable|exists:tenant.units,id',
            'TaxNet'           => 'nullable|numeric|min:0|max:100',
            'tax_method'       => 'nullable|string|in:1,2',
            'note'             => 'nullable|string',
            'stock_alert'      => 'nullable|numeric|min:0',
            'is_variant'       => 'nullable|boolean',
            'is_active'        => 'nullable|boolean',
            'status'           => 'nullable|in:0,1',
            'image'            => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
            'images.*'         => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
        ];

        return $roles;
    }

    public function messages()
    {
        $messages = [
            'code.required'        => trans('validation.required'),
            'code.unique'          => trans('validation.unique'),
            'Type_barcode.required'=> trans('validation.required'),
            'name.required'        => trans('validation.required'),
            'cost.required'        => trans('validation.required'),
            'price.required'       => trans('validation.required'),
            'category_id.required' => trans('validation.required'),
            'category_id.exists'   => trans('validation.exists'),
            'brand_id.exists'      => trans('validation.exists'),
            'unit_id.required'     => trans('validation.required'),
            'unit_id.exists'       => trans('validation.exists'),
            'unit_sale_id.exists'  => trans('validation.exists'),
            'unit_purchase_id.exists' => trans('validation.exists'),
            'image.file'           => trans('validation.file'),
            'image.image'          => trans('validation.image'),
            'image.mimes'          => trans('validation.mimes'),
            'image.max'            => trans('validation.max'),
        ];

        return $messages;
    }

    public function authorize(): bool
    {
        return true;
    }
}
