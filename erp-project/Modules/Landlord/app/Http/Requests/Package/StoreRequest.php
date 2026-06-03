<?php

namespace Modules\Landlord\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|unique:landlord.packages,slug',
            'price'       => 'required|numeric|min:0',
            'limit_users' => 'required|integer|min:-1',
            'limit_tenants' => 'sometimes|integer|min:1',
            'features'    => 'nullable|array',
            'features.*'  => 'boolean',
            'is_active'   => 'boolean',
        ];
    }
}
