<?php

namespace App\Http\Requests\ProjectUpdate;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'report_id' => 'required|exists:reports,id',

            'title' => 'required|string|max:150',

            'description' => 'required|string',

            'images' => 'nullable|array|max:5',

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ]
        ];
    }
}