<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisualizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topics' => 'required|array|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'topics.required' => 'You must select at least one topic.',
        ];
    }
}
