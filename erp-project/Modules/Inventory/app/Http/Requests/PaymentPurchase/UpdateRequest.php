<?php

namespace Modules\Inventory\Http\Requests\PaymentPurchase;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'date'      => 'required|date',
            'Reglement' => 'required|string',
            'montant'   => 'required|numeric|min:0.01',
            'change'    => 'nullable|numeric|min:0',
            'notes'     => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'date.required'      => trans('validation.required'),
            'Reglement.required' => trans('validation.required'),
            'montant.required'   => trans('validation.required'),
            'montant.min'        => trans('validation.min'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
