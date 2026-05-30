<?php

namespace Modules\Landlord\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'group' => 'required|string|max:50',
            'key'   => 'required|string|max:100',
            'text'  => 'required|array',
        ];
    }
}
