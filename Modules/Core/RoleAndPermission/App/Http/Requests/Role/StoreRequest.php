<?php

namespace Modules\Core\RoleAndPermission\App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [] ;

        foreach (config('myConfig.langs') as $lang) {
            $roles ['name.'.$lang] = 'required|string|min:5|unique:roles,name->'.$lang.',NULL,id';
        }

        $roles = array_merge($roles, [
            'permission_ids'   => 'required|array',
            'permission_ids.*' => 'required|exists:permissions,id',
        ]);

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
            'name.required'             => trans('validation.required'),
            'name.string'               => trans('validation.string'),
            'name.min'                  => trans('validation.min.string', ['min' => 5]),
            'name.unique'               => trans('validation.unique'),
            'permission_ids.required'   => trans('validation.required'),
            'permission_ids.array'      => trans('validation.array'),
            'permission_ids.*.required' => trans('validation.required'),
            'permission_ids.*.exists'   => trans('validation.exists'),
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
