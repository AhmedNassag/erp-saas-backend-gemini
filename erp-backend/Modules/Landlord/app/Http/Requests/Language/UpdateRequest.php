<?php

namespace Modules\Landlord\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('language')?->id ?? $this->language;
        return [
            'name'        => 'sometimes|string|max:100',
            'native_name' => 'nullable|string|max:100',
            'direction'   => 'sometimes|in:ltr,rtl',
            'is_default'  => 'boolean',
            'is_active'   => 'boolean',
        ];
    }
}
