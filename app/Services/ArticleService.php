<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService
{
    public function __construct(private readonly ArticleRepository $repository) {}

    public function list(array $filters, ?User $user, int $perPage = 20): LengthAwarePaginator
    {
        $sanitized = $this->sanitizeFilters($filters);

        if ($user) {
            $sanitized = $this->mergePreferences($sanitized, $user);
        }

        return $this->repository->paginate($sanitized, $perPage);
    }

    private function mergePreferences(array $filters, User $user): array
    {
        $user->load('sources', 'categories', 'authors');

        if (empty($filters['source_ids'])) {
            $filters['source_ids'] = $user->sources->pluck('id')->all();
        }

        if (empty($filters['category_ids'])) {
            $filters['category_ids'] = $user->categories->pluck('id')->all();
        }

        if (empty($filters['author_ids'])) {
            $filters['author_ids'] = $user->authors->pluck('id')->all();
        }

        return $filters;
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