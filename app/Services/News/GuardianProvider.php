<?php

namespace App\Services\News;

use App\Services\News\Contracts\NewsProviderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GuardianProvider implements NewsProviderInterface
{
    public function __construct(private readonly string $apiKey) {}

    public function key(): string
    {
        return 'guardian';
    }

    public function name(): string
    {
        return 'The Guardian';
    }

    public function fetch(array $params = []): Collection
    {
        $response = Http::retry(3, 200)
            ->timeout(15)
            ->get('https://content.guardianapis.com/search', array_merge([
                'api-key'     => $this->apiKey,
                'show-fields' => 'byline,trailText',
                'page-size'   => 20,
                'order-by'    => 'newest',
            ], $params));

        $response->throw();

        $results = $response->json('response.results') ?? [];

        return collect($results)->map(fn ($r) => [
            'title'        => $r['webTitle'] ?? null,
            'description'  => $r['fields']['trailText'] ?? null,
            'url'          => $r['webUrl'] ?? null,
            'published_at' => $r['webPublicationDate'] ?? null,
            'authors'      => $this->parseByline($r['fields']['byline'] ?? null),
            'category'     => $r['sectionName'] ?? null,
        ])->filter(fn ($a) => ! empty($a['title']) && ! empty($a['url']));
    }

    /**
     * "Sam Diss" -> ["Sam Diss"]
     * "Alice Smith and Bob Jones" -> ["Alice Smith", "Bob Jones"]
     */
    private function parseByline(?string $byline): array
    {
        if (! $byline) {
            return [];
        }

        $parts = preg_split('/\s+and\s+|,\s+/i', $byline);

        return array_values(array_filter(array_map('trim', $parts)));
    }
}