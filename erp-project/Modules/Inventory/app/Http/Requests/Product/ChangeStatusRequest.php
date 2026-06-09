<?php

namespace Modules\Inventory\Http\Requests\Product;

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
        $messages = [
            'status.required' => trans('validation.required'),
            'status.in'       => trans('validation.in'),
        ];

        return $messages;
    }

    public function authorize(): bool
    {
        return true;
    }
}
