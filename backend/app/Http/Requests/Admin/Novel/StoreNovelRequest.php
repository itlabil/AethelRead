<?php

namespace App\Http\Requests\Admin\Novel;

use Illuminate\Foundation\Http\FormRequest;

class StoreNovelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255', 'unique:novels,name'],
            'type'      => ['required', 'in:manga,manhwa,manhua,other'],
            'is_active' => ['sometimes', 'boolean'],
            'cover'     => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Novel name is required.',
            'name.unique'   => 'Novel name already exists.',
            'type.required' => 'Novel type is required.',
            'type.in'       => 'Invalid novel type.',
            'cover.image'   => 'The cover must be an image.',
            'cover.mimes'   => 'The cover must be a file of type: jpeg, jpg, png, webp.',
            'cover.max'     => 'The cover may not be greater than 2048 kilobytes.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}