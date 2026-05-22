<?php

namespace Modules\Core\Area\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [];



        $roles = array_merge($roles, [
            'status' => 'required|in:0,1',
            'city_id' => 'required|exists:cities,id',
            'name' => [
                'required',
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
