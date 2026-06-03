<?php

namespace Modules\Core\Http\Requests\Area;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [
            'name'    => 'required|string|unique:tenant.areas,name,'.$this->id.',id,deleted_at,NULL',
            'city_id' => 'required|exists:tenant.cities,id',
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
            'name.required'    => trans('validation.required'),
            'name.string'      => trans('validation.string'),
            'name.unique'      => trans('validation.unique'),
            'city_id.required' => trans('validation.required'),
            'city_id.exists'   => trans('validation.exists'),
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