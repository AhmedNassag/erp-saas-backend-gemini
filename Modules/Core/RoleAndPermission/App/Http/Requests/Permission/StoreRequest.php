<?php

namespace Modules\Core\RoleAndPermission\App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [
            'name' => 'required|string|unique:permissions,name,NULL,id,deleted_at,NULL',
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
        return [
            'name.required' => trans('validation.required'),
            'name.string'   => trans('validation.string'),
            'name.unique'   => trans('validation.unique'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
