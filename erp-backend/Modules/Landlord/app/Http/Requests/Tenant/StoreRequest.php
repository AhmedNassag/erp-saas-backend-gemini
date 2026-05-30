<?php

namespace Modules\Landlord\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'domain'    => 'required|string|unique:landlord.tenants,domain',
            'database'  => 'required|string|unique:landlord.tenants,database',
            'package_id' => 'required|exists:landlord.packages,id',
            'status'    => 'sometimes|in:active,suspended,expired',
        ];
    }
}
