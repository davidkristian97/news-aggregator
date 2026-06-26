<?php

namespace App\Services;

use App\Repositories\FilterRepository;
use Illuminate\Support\Collection;

class FilterService
{
    public function __construct(private readonly FilterRepository $repository) {}

    public function sources(string $q): array
    {
        return $this->toIdNameArray($this->repository->searchSources($q));
    }

    public function categories(): array
    {
        return $this->toIdNameArray($this->repository->allCategories());
    }

    public function authors(string $q): array
    {
        return $this->toIdNameArray($this->repository->searchAuthors($q));
    }

    private function toIdNameArray(Collection $collection): array
    {
        return $collection
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->all();
    }
}
