<?php

namespace Modules\Core\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'name'      => 'required|string|unique:tenant.warehouses,name,'.$this->id.',id,deleted_at,NULL',
            'mobile'    => 'required|unique:tenant.warehouses,mobile,'.$this->id.',id,deleted_at,NULL',
            'branch_id' => 'required|exists:tenant.branches,id',
            'area_id'   => 'required|exists:tenant.areas,id',
            'address'   => 'nullable|string',
            'is_main'   => 'nullable|boolean',
            'image'     => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
            'images.*'  => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
        ];

        return $roles;
    }

    public function messages()
    {
        $messages = [
            'name.required'      => trans('validation.required'),
            'name.string'        => trans('validation.string'),
            'name.unique'        => trans('validation.unique'),
            'mobile.required'    => trans('validation.required'),
            'mobile.unique'      => trans('validation.unique'),
            'branch_id.required' => trans('validation.required'),
            'branch_id.exists'   => trans('validation.exists'),
            'area_id.required'   => trans('validation.required'),
            'area_id.exists'     => trans('validation.exists'),
            'address.nullable'   => trans('validation.nullable'),
            'address.string'     => trans('validation.string'),
            'is_main.nullable'   => trans('validation.nullable'),
            'is_main.boolean'    => trans('validation.boolean'),
            'image.nullable'     => trans('validation.nullable'),
            'image.file'         => trans('validation.file'),
            'image.image'        => trans('validation.image'),
            'image.mimes'        => trans('validation.mimes'),
            'image.max'          => trans('validation.max'),
            'images.*.nullable'  => trans('validation.nullable'),
            'images.*.file'      => trans('validation.file'),
            'images.*.image'     => trans('validation.image'),
            'images.*.mimes'     => trans('validation.mimes'),
            'images.*.max'       => trans('validation.max'),
        ];

        return $messages;
    }

    public function authorize(): bool
    {
        return true;
    }
}
