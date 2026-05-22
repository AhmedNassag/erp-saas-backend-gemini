<?php

namespace Modules\Core\Branch\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $roles = [] ;

        foreach (config('myConfig.langs') as $lang)
        {
            $roles ['name.'.$lang] = 'required|unique:branches,name->'.$lang.',NULL,id,deleted_at,NULL';
        }

        $roles = array_merge($roles, [
            'code'    => 'required|unique:branches,branch_code,NULL,id,deleted_at,NULL',
            'mobile'  => 'required|unique:branches,mobile,NULL,id,deleted_at,NULL',
            'address' => 'nullable|string',
            'status'  => 'required|in:0,1',
            'area_id' => 'required|exists:areas,id',
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

        // Add name messages for each language
        foreach (config('myConfig.langs') as $lang) {
            $messages['name.' . $lang . '.required'] = __('validation.required', ['attribute' => __('branch.name') . ' (' . strtoupper($lang) . ')']);
            $messages['name.' . $lang . '.unique']   = __('validation.unique', ['attribute' => __('branch.name') . ' (' . strtoupper($lang) . ')']);
        }

        // Add other field messages
        $messages = array_merge($messages, [
            'code.required'    => __('validation.required', ['attribute' => __('branch.code')]),
            'code.unique'      => __('validation.unique', ['attribute' => __('branch.code')]),
            'mobile.required'  => __('validation.required', ['attribute' => __('branch.mobile')]),
            'mobile.unique'    => __('validation.unique', ['attribute' => __('branch.mobile')]),
            'address.nullable' => __('validation.nullable', ['attribute' => __('branch.address')]),
            'address.string'   => __('validation.string', ['attribute' => __('branch.address')]),
            'status.required'  => __('validation.required', ['attribute' => __('branch.status')]),
            'status.in'        => __('validation.in', ['attribute' => __('branch.status')]),
            'area_id.required' => __('validation.required', ['attribute' => __('branch.area_id')]),
            'area_id.exists'   => __('validation.exists', ['attribute' => __('branch.area_id')]),
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
