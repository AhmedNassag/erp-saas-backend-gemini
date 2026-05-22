<?php

namespace Modules\Core\City\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [];

        foreach (config('myConfig.langs') as $lang) {
            $roles['name.' . $lang] = 'required|unique:cities,name->' . $lang . ',NULL,id,deleted_at,NULL';
        }

        $roles = array_merge($roles, [
            'status'     => 'required|in:0,1',
            'country_id' => 'nullable|exists:countries,id',
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
        $messages = [];

        foreach (config('myConfig.langs') as $lang) {
            $messages['name.' . $lang . '.required'] = __('validation.required', ['attribute' => __('city::cities.name') . ' (' . $lang . ')']);
            $messages['name.' . $lang . '.unique']   = __('validation.unique', ['attribute' => __('city::cities.name') . ' (' . $lang . ')']);
        }

        $messages = array_merge($messages, [
            'status.required'   => __('validation.required', ['attribute' => __('city::cities.status')]),
            'status.in'         => __('validation.in', ['attribute' => __('city::cities.status')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('city::cities.country')]),
        ]);

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