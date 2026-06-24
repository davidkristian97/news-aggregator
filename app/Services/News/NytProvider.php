<?php

namespace App\Services\News;

use App\Services\News\Contracts\NewsProviderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NytProvider implements NewsProviderInterface
{
    public function __construct(private readonly string $apiKey) {}

    public function key(): string
    {
        return 'nyt';
    }

    public function name(): string
    {
        return 'The New York Times';
    }

    public function fetch(array $params = []): Collection
    {
        $response = Http::retry(3, 200)
            ->timeout(15)
            ->get('https://api.nytimes.com/svc/search/v2/articlesearch.json', array_merge([
                'api-key' => $this->apiKey,
                'sort' => 'newest'
            ], $params));

        $response->throw();

        $docs = $response->json('response.docs') ?? [];

        $docs = array_filter($docs, fn ($doc) => ($doc['news_desk'] ?? null) !== 'Corrections');

        return collect($docs)->map(fn ($doc) => [
            'title'        => $doc['headline']['main'] ?? null,
            'description'  => $doc['abstract'] ?? $doc['snippet'] ?? null,
            'url'          => $doc['web_url'] ?? null,
            'published_at' => $doc['pub_date'] ?? null,
            'authors'      => $this->parseAuthors($doc['byline']['original'] ?? null),
            'category'     => $doc['section_name'] ?? null,
        ])->filter(fn ($a) => ! empty($a['title']) && ! empty($a['url']));
    }

    private function parseAuthors(?string $byline): array
    {
        if (! $byline) {
            return [];
        }

        $clean = preg_replace('/^By\s+/i', '', $byline);
        $parts = preg_split('/\s+and\s+|,\s+/i', $clean);

        return array_values(array_filter(array_map('trim', $parts)));
    }
}