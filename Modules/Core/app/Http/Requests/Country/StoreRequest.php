<?php

namespace Modules\Core\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [
            'status' => 'required|in:0,1',
            'image'  => 'nullable|file|image|mimes:png,jpg,jpeg|max:5000',
        ] ;

        foreach (config('myConfig.langs') as $lang)
        {
            $roles ['name.'.$lang] = 'required|unique:countries,name->'.$lang.',NULL,id,deleted_at,NULL';
        }

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
            'status.required' => __('validation.required', ['attribute' => __('country.status')]),
            'status.in'       => __('validation.in', ['attribute' => __('country.status')]),
            'image.nullable'  => __('validation.nullable', ['attribute' => __('country.image')]),
            'image.file'      => __('validation.file', ['attribute' => __('country.image')]),
            'image.image'     => __('validation.image', ['attribute' => __('country.image')]),
            'image.mimes'     => __('validation.mimes', ['attribute' => __('country.image'), 'values' => 'png, jpg, jpeg']),
            'image.max'       => __('validation.max.file', ['attribute' => __('country.image'), 'max' => 5000]),
        ];

        // Add name messages for each language
        foreach (config('myConfig.langs') as $lang) {
            $messages['name.' . $lang . '.required'] = __('validation.required', ['attribute' => __('country.name') . ' (' . strtoupper($lang) . ')']);
            $messages['name.' . $lang . '.unique']   = __('validation.unique', ['attribute' => __('country.name') . ' (' . strtoupper($lang) . ')']);
        }

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
