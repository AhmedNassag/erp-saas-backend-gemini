<?php

namespace Modules\Core\Http\Requests\Area;

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

        foreach (config('myConfig.langs') as $lang) {
            $roles['name.' . $lang] = 'required|unique:areas,name->' . $lang . ',' . $this->area->id . ',id,deleted_at,NULL';
        }

        $roles = array_merge($roles, [
            'status' => 'required|in:0,1',
            'city_id' => 'required|exists:cities,id',
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
            $messages['name.' . $lang . '.required'] = __('validation.required', ['attribute' => __('area::areas.name') . ' (' . $lang . ')']);
            $messages['name.' . $lang . '.unique']   = __('validation.unique', ['attribute' => __('area::areas.name') . ' (' . $lang . ')']);
        }

        $messages = array_merge($messages, [
            'status.required' => __('validation.required', ['attribute' => __('area::areas.status')]),
            'status.in'       => __('validation.in', ['attribute' => __('area::areas.status')]),
            'city_id.exists'  => __('validation.exists', ['attribute' => __('area::areas.city')]),
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