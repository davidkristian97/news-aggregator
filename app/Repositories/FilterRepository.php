<?php

namespace App\Repositories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Support\Collection;

class FilterRepository
{
    public function searchSources(string $q): Collection
    {
        return Source::where('name', 'like', "%{$q}%")->orderBy('name')->get();
    }

    public function allCategories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function searchAuthors(string $q): Collection
    {
        return Author::where('name', 'like', "%{$q}%")->orderBy('name')->get();
    }
}
