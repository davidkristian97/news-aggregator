<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexArticlesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_ids' => ['array'],
            'source_ids.*' => ['integer', 'exists:sources,id'],
            'category_ids' => ['array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'author_ids' => ['array'],
            'author_ids.*' => ['integer', 'exists:authors,id'],
            
            'q' => ['string', 'min:1', 'max:200'],
            'from' => ['date'],
            'to' => ['date', 'after_or_equal:from'],
            'per_page' => ['integer', 'min:1', 'max:100'],
            'page' => ['integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        return [
            'source_ids' => $this->input('source_ids', []),
            'category_ids' => $this->input('category_ids', []),
            'author_ids' => $this->input('author_ids', []),
            'q' => $this->input('q'),
            'from' => $this->input('from'),
            'to' => $this->input('to'),
        ];
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 20);
    }
}