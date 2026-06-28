<?php

namespace App\Console\Commands;

use App\Services\ArticleStorageService;
use App\Services\News\Contracts\NewsProviderInterface;
use App\Services\News\NewsAggregator;
use Illuminate\Console\Command;

class FetchArticlesCommand extends Command
{
    protected $signature = 'articles:fetch {--source= : Limit to one provider key: nyt, newsapi, or guardian}';

    protected $description = 'Fetch articles from all (or one) news provider and store them in the database';

    public function __construct(
        private readonly NewsAggregator $aggregator,
        private readonly ArticleStorageService $storage,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $providers = $this->aggregator->providers();

        if ($sourceKey = $this->option('source')) {
            $providers = array_filter($providers, fn ($p) => $p->key() === $sourceKey);

            if (empty($providers)) {
                $this->error("Unknown source \"{$sourceKey}\". Available: " . implode(', ', array_map(fn ($p) => $p->key(), $this->aggregator->providers())));
                return self::FAILURE;
            }
        }

        foreach ($providers as $provider) {
            $this->info("Fetching from {$provider->name()}...");

            try {
                $count = $provider->key() === 'newsapi'
                    ? $this->fetchNewsApi($provider)
                    : $this->storage->saveArticles($provider->fetch(), $provider->name());

                $this->info("{$count} articles saved.");
            } catch (\Throwable $e) {
                // One provider failing should not stop the others.
                $this->error("Failed: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }

    private function fetchNewsApi(NewsProviderInterface $provider): int
    {
        $categories = ['Business', 'Entertainment', 'General', 'Health', 'Science', 'Sports', 'Technology'];
        $saved = 0;

        foreach ($categories as $category) {
            $articles = $provider->fetch(['category' => $category]);
            $saved   += $this->storage->saveArticles($articles, $provider->name());
        }

        return $saved;
    }
}
