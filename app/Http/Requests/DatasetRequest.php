<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatasetRequest extends FormRequest
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
            'name' => 'required|string|min:5',
            'indicators' => 'required|exists:indicators,id',
            'dimensions' => 'required|exists:dimensions,id',
            'fact_table' => 'required',
            'max_area_level' => 'required',
            'topics' => 'required|array|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'indicators.required' => 'You must select at least one indicator.',
            'dimensions.required' => 'You must select at least one dimension.',
            'topics.required' => 'You must select at least one topic.',
        ];
    }

    public function attributes(): array
    {
        return [
            'max_area_level' => 'data geographic granularity',
        ];
    }
}
