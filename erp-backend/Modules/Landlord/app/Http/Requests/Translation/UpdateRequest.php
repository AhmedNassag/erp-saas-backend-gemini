<?php

namespace Modules\Landlord\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'text' => 'required|array',
        ];
    }
}
