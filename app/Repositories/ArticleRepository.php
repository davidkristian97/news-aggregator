<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository
{
    public function paginate(array $filters, int $perPage = 20, array $preferences = []): LengthAwarePaginator
    {
        $query = Article::query()->with(['source', 'category', 'authors']);

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

        $score = $this->preferenceScore($preferences);
        if ($score !== null) {
            $query->orderByRaw("{$score} DESC");
        }

        $query->orderBy('published_at', 'DESC');

        return $query->paginate($perPage);
    }

    private function preferenceScore(array $preferences): ?string
    {
        $parts = [];

        if (!empty($preferences['source_ids'])) {
            $ids = implode(',', $preferences['source_ids']);
            $parts[] = "CASE WHEN source_id IN ({$ids}) THEN 1 ELSE 0 END";
        }

        if (!empty($preferences['category_ids'])) {
            $ids = implode(',', $preferences['category_ids']);
            $parts[] = "CASE WHEN category_id IN ({$ids}) THEN 1 ELSE 0 END";
        }

        if (!empty($preferences['author_ids'])) {
            $ids = implode(',', $preferences['author_ids']);
            $parts[] = "CASE WHEN EXISTS (SELECT 1 FROM article_authors WHERE article_authors.article_id = articles.id AND article_authors.author_id IN ({$ids})) THEN 1 ELSE 0 END";
        }

        return empty($parts) ? null : '(' . implode(' + ', $parts) . ')';
    }
}
