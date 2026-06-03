<?php

namespace Modules\Landlord\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('tenant')?->id ?? $this->tenant;
        return [
            'name'      => 'sometimes|string|max:255',
            'domain'    => 'sometimes|string|unique:landlord.tenants,domain,' . $id,
            'package_id' => 'sometimes|exists:landlord.packages,id',
            'status'    => 'sometimes|in:active,suspended,expired',
        ];
    }
}
