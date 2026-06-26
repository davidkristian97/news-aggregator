<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source_ids' => ['array'],
            'source_ids.*' => ['integer', 'exists:sources,id'],
            'category_ids' => ['array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'author_ids' => ['array'],
            'author_ids.*' => ['integer', 'exists:authors,id'],
        ];
    }

    public function preferences(): array
    {
        return $this->only('source_ids', 'category_ids', 'author_ids');
    }
}
