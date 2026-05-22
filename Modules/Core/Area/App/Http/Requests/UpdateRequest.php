<?php

namespace Modules\Core\Area\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [];

        // foreach (config('myConfig.langs') as $lang)
        // {
        //     $roles ['name.'.$lang] = 'required|unique:areas,name->'.$lang.','.$this->area->id.',id,deleted_at,NULL';
        // }

        $roles = array_merge($roles, [

            'status' => 'nullable|in:0,1',
            'city_id' => 'nullable|exists:cities,id',
            'name' => [
                'nullable',
                Rule::unique('areas', 'name')->whereNull('deleted_at')
            ],
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
            'name.en.required' => trans('validation.required'),
            'name.ar.required' => trans('validation.required'),
            'name.string'   => trans('validation.string'),

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