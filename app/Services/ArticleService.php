<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService
{
    public function __construct(private readonly ArticleRepository $repository) {}

    public function list(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $sanitized = $this->sanitizeFilters($filters);
        return $this->repository->paginate($sanitized, $perPage);
    }

    private function sanitizeFilters(array $filters): array
    {
        return [
            'source_ids' => $this->sanitizeIds($filters['source_ids'] ?? []),
            'category_ids' => $this->sanitizeIds($filters['category_ids'] ?? []),
            'author_ids' => $this->sanitizeIds($filters['author_ids'] ?? []),
            'from' => $filters['from'] ?? null,
            'to' => $filters['to'] ?? null,
            'q' => isset($filters['q']) ? trim((string) $filters['q']) : null,
        ];
    }

    private function sanitizeIds(array $ids): array
    {
        return collect($ids)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
}