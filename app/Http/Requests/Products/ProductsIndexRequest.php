<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductsIndexRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'limit'      => ['sometimes', 'integer', 'min:1', 'max:50'],
            'offset'     => ['sometimes', 'integer', 'min:0'],
            'sort'       => ['sometimes', 'string', Rule::in(['id', 'price', 'name', 'created_at'])],
            'order'      => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'section_id' => ['sometimes', 'integer', 'exists:sections,id'],
        ];
    }

    public function params(): array
    {
        $validated = $this->validated();

        return [
            'limit'  => (int) ($validated['limit'] ?? 10),
            'offset' => (int) ($validated['offset'] ?? 0),
            'sort'   => (string) ($validated['sort'] ?? 'id'),
            'order'  => (string) ($validated['order'] ?? 'desc'),
            'section_id' => isset($validated['section_id']) ? (int) ($validated['section_id']) : null,
        ];
    }
}
