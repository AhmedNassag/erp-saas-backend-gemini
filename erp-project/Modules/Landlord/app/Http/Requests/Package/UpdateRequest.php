<?php

namespace Modules\Landlord\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('package')?->id ?? $this->package;
        return [
            'name'        => 'sometimes|string|max:100',
            'slug'        => 'sometimes|string|unique:landlord.packages,slug,' . $id,
            'price'       => 'sometimes|numeric|min:0',
            'limit_users' => 'sometimes|integer|min:-1',
            'limit_tenants' => 'sometimes|integer|min:1',
            'features'    => 'nullable|array',
            'features.*'  => 'boolean',
            'is_active'   => 'boolean',
        ];
    }
}
