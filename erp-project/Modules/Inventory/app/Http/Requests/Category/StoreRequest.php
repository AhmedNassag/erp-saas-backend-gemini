<?php

namespace Modules\Inventory\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        $roles = [
            'name'        => 'required|string|unique:tenant.categories,name,NULL,id,deleted_at,NULL',
            'code'        => 'required|unique:tenant.categories,code,NULL,id,deleted_at,NULL',
            'is_main'     => 'nullable|boolean',
            'category_id' => [
                Rule::requiredIf(function () {
                    return !$this->boolean('is_main');
                }),
                'nullable',
                'exists:tenant.categories,id',
            ],
            'image'    => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
            'images.*' => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
        ];

        return $roles;
    }

    public function messages()
    {
        $messages = [
            'name.required'        => trans('validation.required'),
            'name.string'          => trans('validation.string'),
            'name.unique'          => trans('validation.unique'),
            'code.required'        => trans('validation.required'),
            'code.unique'          => trans('validation.unique'),
            'is_main.boolean'      => trans('validation.boolean'),
            'category_id.required' => trans('validation.required'),
            'category_id.exists'   => trans('validation.exists'),
            'image.nullable'       => trans('validation.nullable'),
            'image.file'           => trans('validation.file'),
            'image.image'          => trans('validation.image'),
            'image.mimes'          => trans('validation.mimes'),
            'image.max'            => trans('validation.max'),
            'images.*.nullable'    => trans('validation.nullable'),
            'images.*.file'        => trans('validation.file'),
            'images.*.image'       => trans('validation.image'),
            'images.*.mimes'       => trans('validation.mimes'),
            'images.*.max'         => trans('validation.max'),
        ];

        return $messages;
    }

    public function authorize(): bool
    {
        return true;
    }
}
