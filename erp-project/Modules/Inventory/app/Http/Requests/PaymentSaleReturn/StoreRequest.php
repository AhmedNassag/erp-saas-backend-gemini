<?php

namespace Modules\Inventory\Http\Requests\PaymentSaleReturn;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'sale_return_id' => 'required|exists:tenant.sale_returns,id',
            'date'           => 'required|date',
            'Reglement'      => 'required|string',
            'montant'        => 'required|numeric|min:0.01',
            'change'         => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'sale_return_id.required' => trans('validation.required'),
            'date.required'           => trans('validation.required'),
            'Reglement.required'      => trans('validation.required'),
            'montant.required'        => trans('validation.required'),
            'montant.min'             => trans('validation.min'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
