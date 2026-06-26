<?php

namespace App\Services\News;

use App\Services\News\Contracts\NewsProviderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NewsApiProvider implements NewsProviderInterface
{
    public function __construct(private readonly string $apiKey) {}

    public function key(): string
    {
        return 'newsapi';
    }

    public function name(): string
    {
        return 'NewsAPI.org';
    }

    public function fetch(array $params = []): Collection
    {
        $response = Http::retry(3, 200)
            ->timeout(15)
            ->get('https://newsapi.org/v2/top-headlines', array_merge([
                'apiKey' => $this->apiKey,
                'language' => 'en',
                'pageSize' => 20,
                'sortBy' => 'publishedAt',
            ], $params));

        $response->throw();

        $articles = $response->json('articles') ?? [];

        return collect($articles)->map(fn ($a) => [
            'title'        => $a['title'] ?? null,
            'description'  => $a['description'] ?? $a['content'] ?? null,
            'url'          => $a['url'] ?? null,
            'published_at' => $a['publishedAt'] ?? null,
            'authors'      => $a['author'] ? array_map('trim', explode(',', $a['author'])) : [],
            'category'     => $params['category'] ?? null,
            'source'       => $a['source']['name'] ?? null,
        ])->filter(fn ($a) => ! empty($a['title']) && ! empty($a['url']));
    }
}