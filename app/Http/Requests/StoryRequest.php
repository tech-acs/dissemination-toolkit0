<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'topic_id' => 'required',
            //'template_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'topic_id.required' => 'The topic field is required',
            //'template_id.required' => 'You must select a story template'
        ];
    }
}
