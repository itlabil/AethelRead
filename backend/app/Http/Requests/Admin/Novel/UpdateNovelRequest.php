<?php

namespace App\Http\Requests\Admin\Novel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNovelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $novelId = $this->route('novel');

        return [
            'name'      => ['required', 'string', 'max:255', "unique:novels,name,{$novelId}"],
            'type'      => ['required', 'in:manga,manhwa,manhua,other'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Novel name is required.',
            'name.unique'   => 'Novel name already exists.',
            'type.required' => 'Novel type is required.',
            'type.in'       => 'Invalid novel type.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}