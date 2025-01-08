<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ArticleSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'term' => 'required|string|min:2|max:100',
            'per_page' => 'nullable|integer|min:1|max:50'
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}

