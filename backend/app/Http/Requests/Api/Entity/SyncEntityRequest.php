<?php

namespace App\Http\Requests\Api\Entity;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SyncEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hashes'   => ['present', 'array'],
            'hashes.*' => ['string', 'size:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'hashes.present' => 'Hashes field must be present.',
            'hashes.array'   => 'Hashes must be an array.',
            'hashes.*.size'  => 'Each hash must be a valid SHA-256 string (64 characters).',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}