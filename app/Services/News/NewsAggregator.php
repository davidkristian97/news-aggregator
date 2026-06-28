<?php

namespace App\Services\News;

use App\Services\News\Contracts\NewsProviderInterface;

class NewsAggregator
{
    /** @param NewsProviderInterface[] $providers */
    public function __construct(private readonly array $providers) {}

    /** @return NewsProviderInterface[] */
    public function providers(): array
    {
        return $this->providers;
    }
}
