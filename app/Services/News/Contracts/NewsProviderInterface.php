<?php

namespace App\Services\News\Contracts;

use Illuminate\Support\Collection;

interface NewsProviderInterface
{
    /** Unique key like 'nyt', 'newsapi', 'guardian' */
    public function key(): string;

    /** Display name like 'The New York Times' */
    public function name(): string;

    /** Fetch articles; return raw associative arrays from provider */
    public function fetch(array $params = []): Collection;
}