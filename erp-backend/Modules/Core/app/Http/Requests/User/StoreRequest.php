<?php

namespace Modules\Core\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [
            'name'          => 'required|string',
            'email'         => 'required|email|unique:tenant.users,email,NULL,id,deleted_at,NULL',
            'password'      => 'required|string|min:8',
            'role_ids'      => 'array|exists:tenant.roles,id',
            'department_id' => 'nullable|exists:tenant.departments,id',
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
            'name.required'        => trans('validation.required'),
            'name.string'          => trans('validation.string'),
            'email.required'       => trans('validation.required'),
            'email.email'          => trans('validation.email'),
            'email.unique'         => trans('validation.unique'),
            'password.required'    => trans('validation.required'),
            'password.string'      => trans('validation.string'),
            'password.min'         => trans('validation.min'),
            'role_ids.array'       => trans('validation.array'),
            'role_ids.exists'      => trans('validation.exists'),
            'department_id.exists' => trans('validation.exists'),
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
