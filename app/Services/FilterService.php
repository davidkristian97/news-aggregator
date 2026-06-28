<?php

namespace App\Services;

use App\Repositories\FilterRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FilterService
{
    private const TTL = 3600;

    public function __construct(private readonly FilterRepository $repository) {}

    public function sources(?string $q): array
    {
        $key = $q ? "filters.sources.{$q}" : 'filters.sources.all';

        return Cache::tags(['filters'])->remember(
            $key,
            self::TTL,
            fn () => $this->toIdNameArray($this->repository->searchSources($q)
        ));
    }

    public function categories(): array
    {
        return Cache::tags(['filters'])->remember(
            'filters.categories',
            self::TTL,
            fn () => $this->toIdNameArray($this->repository->allCategories()
        ));
    }

    public function authors(?string $q): array
    {
        $key = $q ? "filters.authors.{$q}" : 'filters.authors.all';

        return Cache::tags(['filters'])->remember(
            $key,
            self::TTL,
            fn () => $this->toIdNameArray($this->repository->searchAuthors($q)
        ));
    }

    private function toIdNameArray(Collection $collection): array
    {
        return $collection
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->all();
    }
}
