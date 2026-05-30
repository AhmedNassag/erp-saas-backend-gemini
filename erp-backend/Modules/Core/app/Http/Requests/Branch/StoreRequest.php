<?php

namespace Modules\Core\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [
            'name'     => 'required|string|unique:tenant.branches,name,NULL,id,deleted_at,NULL',
            'code'     => 'required|unique:tenant.branches,branch_code,NULL,id,deleted_at,NULL',
            'mobile'   => 'required|unique:tenant.branches,mobile,NULL,id,deleted_at,NULL',
            'address'  => 'nullable|string',
            'area_id'  => 'required|exists:tenant.areas,id',
            'image'    => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
            'images.*' => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
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
            'name.required'     => trans('validation.required'),
            'name.string'       => trans('validation.string'),
            'name.unique'       => trans('validation.unique'),
            'code.required'     => trans('validation.required'),
            'code.unique'       => trans('validation.unique'),
            'mobile.required'   => trans('validation.required'),
            'mobile.unique'     => trans('validation.unique'),
            'address.nullable'  => trans('validation.nullable'),
            'address.string'    => trans('validation.string'),
            'area_id.required'  => trans('validation.required'),
            'area_id.exists'    => trans('validation.exists'),
            'image.nullable'    => trans('validation.nullable'),
            'image.file'        => trans('validation.file'),
            'image.image'       => trans('validation.image'),
            'image.mimes'       => trans('validation.mimes'),
            'image.max'         => trans('validation.max'),
            'images.*.nullable' => trans('validation.nullable'),
            'images.*.file'     => trans('validation.file'),
            'images.*.image'    => trans('validation.image'),
            'images.*.mimes'    => trans('validation.mimes'),
            'images.*.max'      => trans('validation.max'),
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
