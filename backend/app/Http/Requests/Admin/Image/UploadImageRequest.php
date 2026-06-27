<?php

namespace App\Http\Requests\Admin\Image;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Image file is required.',
            'image.image'    => 'File must be an image.',
            'image.mimes'    => 'Image must be jpeg, jpg, png, or webp.',
            'image.max'      => 'Image size must not exceed 2MB.',
        ];
    }
}