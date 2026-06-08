<?php

namespace Modules\Inventory\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'name'     => 'required|string|unique:tenant.brands,name,'.$this->id.',id,deleted_at,NULL',
            'code'     => 'required|unique:tenant.brands,code,'.$this->id.',id,deleted_at,NULL',
            'image'    => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
            'images.*' => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
        ];

        return $roles;
    }

    public function messages()
    {
        $messages = [
            'name.required'     => trans('validation.required'),
            'name.string'       => trans('validation.string'),
            'name.unique'       => trans('validation.unique'),
            'code.required'     => trans('validation.required'),
            'code.unique'       => trans('validation.unique'),
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

    public function authorize(): bool
    {
        return true;
    }
}
