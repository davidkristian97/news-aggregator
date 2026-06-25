<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository
{
    public function paginate(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Article::query()
            ->with(['source', 'category', 'authors'])
            ->latest('published_at');

        if (!empty($filters['source_ids'])) {
            $query->fromSources($filters['source_ids']);
        }

        if (!empty($filters['category_ids'])) {
            $query->inCategories($filters['category_ids']);
        }

        if (!empty($filters['author_ids'])) {
            $query->byAuthors($filters['author_ids']);
        }

        if (!empty($filters['from']) || !empty($filters['to'])) {
            $query->publishedBetween($filters['from'] ?? null, $filters['to'] ?? null);
        }

        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        return $query->paginate($perPage);
    }
}