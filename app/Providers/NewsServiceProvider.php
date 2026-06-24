<?php

namespace App\Providers;

use App\Services\News\GuardianProvider;
use App\Services\News\NewsAggregator;
use App\Services\News\NewsApiProvider;
use App\Services\News\NytProvider;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NewsAggregator::class, function () {
            return new NewsAggregator([
                new NytProvider(config('services.nyt.key')),
                new NewsApiProvider(config('services.newsapi.key')),
                new GuardianProvider(config('services.guardian.key')),
            ]);
        });
    }
}