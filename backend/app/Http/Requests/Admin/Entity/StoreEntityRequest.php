<?php

namespace App\Http\Requests\Admin\Entity;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'novel_id'       => ['required', 'uuid', 'exists:novels,id'],
            'type'           => ['required', 'in:character,place,item'],
            'name'           => ['required', 'string', 'max:255'],
            'is_active'      => ['sometimes', 'boolean'],
            'aliases'        => ['sometimes', 'array'],
            'aliases.*'      => ['string', 'max:255', 'distinct'],
            'keywords'       => ['sometimes', 'array'],
            'keywords.*'     => ['string', 'max:255', 'distinct'],
            'description_en' => ['sometimes', 'nullable', 'string'],
            'description_id' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'novel_id.required' => 'Novel is required.',
            'novel_id.exists'   => 'Selected novel does not exist.',
            'type.required'     => 'Entity type is required.',
            'type.in'           => 'Invalid entity type.',
            'name.required'     => 'Entity name is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}