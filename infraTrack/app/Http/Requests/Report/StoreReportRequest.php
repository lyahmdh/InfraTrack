<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',

            'title' => 'required|string|max:150',

            'description' => 'required|string',

            'severity' => 'required|integer|in:1,2,3',

            'is_anonymous' => 'required|boolean',

            'location_name' => 'required|string|max:255',

            'district' => 'required|string|max:100',

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90'
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180'
            ],

            'images' => 'required|array|min:1|max:5',

            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ]
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}