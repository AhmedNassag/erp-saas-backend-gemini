<?php

namespace Modules\Inventory\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Inventory\Models\ProductWarehouse\ProductWarehouse;
use Modules\Inventory\Models\Unit\Unit;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'from_warehouse_id'                   => 'required|exists:tenant.warehouses,id|different:to_warehouse_id',
            'to_warehouse_id'                     => 'required|exists:tenant.warehouses,id|different:from_warehouse_id',
            'date'                                => 'required|date',
            'status'                              => 'required|in:completed,sent,pending',
            'notes'                               => 'nullable|string',
            'tax_rate'                            => 'nullable|numeric|min:0',
            'TaxNet'                              => 'nullable|numeric|min:0',
            'discount'                            => 'nullable|numeric|min:0',
            'shipping'                            => 'nullable|numeric|min:0',
            'GrandTotal'                          => 'nullable|numeric|min:0',
            'details'                             => 'required|array|min:1',
            'details.*.product_id'                => 'required|exists:tenant.products,id',
            'details.*.quantity'                  => 'required|numeric|min:0.01',
            'details.*.cost'                      => 'nullable|numeric|min:0',
            'details.*.TaxNet'                    => 'nullable|numeric|min:0',
            'details.*.tax_method'                => 'nullable|string',
            'details.*.discount'                  => 'nullable|numeric|min:0',
            'details.*.discount_method'           => 'nullable|string',
            'details.*.purchase_unit_id'          => 'nullable|exists:tenant.units,id',
            'details.*.total'                     => 'nullable|numeric|min:0',
            'details.*.product_variant_id'        => 'nullable|exists:tenant.product_variants,id',
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) return;

        $validator->after(function ($validator) {
            $fromWarehouseId = $this->input('from_warehouse_id');
            if (!$fromWarehouseId) return;

            foreach ($this->input('details', []) as $i => $detail) {
                $qty = $detail['quantity'] ?? 0;
                if ($qty <= 0) continue;

                $query = ProductWarehouse::where('warehouse_id', $fromWarehouseId)
                    ->where('product_id', $detail['product_id']);

                if (!empty($detail['product_variant_id'])) {
                    $query->where('product_variant_id', $detail['product_variant_id']);
                } else {
                    $query->whereNull('product_variant_id');
                }

                $pw = $query->first();
                $stockQty = $pw ? $pw->qty : 0;

                $unit = Unit::find($detail['purchase_unit_id'] ?? null);
                $transferQty = $qty;
                if ($unit) {
                    if ($unit->operator == '/') {
                        $transferQty = $qty / $unit->operator_value;
                    } else {
                        $transferQty = $qty * $unit->operator_value;
                    }
                }

                if ($transferQty > $stockQty) {
                    $validator->errors()->add(
                        "details.{$i}.quantity",
                        "الكمية المطلوبة ({$qty}) تتجاوز المخزون المتاح ({$stockQty})"
                    );
                }
            }
        });
    }

    public function messages()
    {
        return [
            'from_warehouse_id.required'         => trans('validation.required'),
            'from_warehouse_id.exists'           => trans('validation.exists'),
            'from_warehouse_id.different'        => trans('validation.different'),
            'to_warehouse_id.required'           => trans('validation.required'),
            'to_warehouse_id.exists'             => trans('validation.exists'),
            'to_warehouse_id.different'          => trans('validation.different'),
            'date.required'                      => trans('validation.required'),
            'status.required'                    => trans('validation.required'),
            'status.in'                          => trans('validation.in'),
            'details.required'                   => trans('validation.required'),
            'details.min'                        => trans('validation.min'),
            'details.*.product_id.required'      => trans('validation.required'),
            'details.*.quantity.required'        => trans('validation.required'),
            'details.*.quantity.min'             => trans('validation.min'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
