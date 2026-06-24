<?php

namespace App\Services\News;

use App\Services\News\Contracts\NewsProviderInterface;
use Illuminate\Support\Collection;

class NewsAggregator
{
    /** @param NewsProviderInterface[] $providers */
    public function __construct(private readonly array $providers) {}

    /**
     * Fetch from every registered provider and merge the results.
     * If one provider fails (rate limit, downtime, bad response), it's
     * reported and skipped rather than failing the entire fetch — the
     * other sources should still come through.
     */
    public function all(): Collection
    {
        $results = collect();

        foreach ($this->providers as $provider) {
            try {
                $results = $results->merge($provider->fetch());
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $results;
    }

    /**
     * Fetch from a single named provider, e.g. bySource('nyt').
     */
    public function bySource(string $key): Collection
    {
        $provider = collect($this->providers)->first(fn ($p) => $p->key() === $key);

        throw_if(!$provider, \InvalidArgumentException::class, "Unknown source: {$key}");

        return $provider->fetch();
    }
}