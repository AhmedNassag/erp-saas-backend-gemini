<?php

namespace Modules\Landlord\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'code'        => 'required|string|max:10|unique:landlord.languages,code',
            'name'        => 'required|string|max:100',
            'native_name' => 'nullable|string|max:100',
            'direction'   => 'required|in:ltr,rtl',
            'is_default'  => 'boolean',
            'is_active'   => 'boolean',
        ];
    }
}
