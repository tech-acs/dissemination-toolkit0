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
            'indicator_id' => 'required|exists:indicators,id',
            'fact_table' => 'required',
            'max_area_level' => 'required',
            'years' => 'required'
        ];
    }

    public function attributes(): array
    {
        return [
            'indicator_id' => 'indicator',
            'max_area_level' => 'data geographic granularity',
        ];
    }
}
