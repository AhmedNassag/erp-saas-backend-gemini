<?php

namespace Modules\Core\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [
            'name'       => 'required|string|unique:tenant.cities,name,'.$this->id.',id,deleted_at,NULL',
            'country_id' => 'required|exists:tenant.countries,id',
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
            'name.required'       => trans('validation.required'),
            'name.string'         => trans('validation.string'),
            'name.unique'         => trans('validation.unique'),
            'country_id.required' => trans('validation.required'),
            'country_id.exists'   => trans('validation.exists'),
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
