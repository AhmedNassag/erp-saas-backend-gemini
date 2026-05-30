<?php

namespace Modules\Core\Http\Requests\RoleAndPermission\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [
            'name'             => 'required|string|min:3|unique:roles,name,'.$this->id.',id,deleted_at,NULL',
            'permission_ids'   => 'required|array',
            // 'permission_ids.*' => 'required|exists:permissions,id',
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
            'name.required'             => trans('validation.required'),
            'name.string'               => trans('validation.string'),
            'name.min'                  => trans('validation.min'),
            'name.unique'               => trans('validation.unique'),
            'permission_ids.required'   => trans('validation.required'),
            'permission_ids.array'      => trans('validation.array'),
            'permission_ids.*.required' => trans('validation.required'),
            'permission_ids.*.exists'   => trans('validation.exists'),
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
