<?php

namespace Modules\Core\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [
            'name' => 'required|string|unique:tenant.countries,name,'.$this->id.',id,deleted_at,NULL',
        ];
        
        return $roles;
    }


    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        $messages = [
            'name.required' => trans('validation.required'),
            'name.string'   => trans('validation.string'),
            'name.unique'   => trans('validation.unique'),
        ];

        return $messages;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
