<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArticleStorageService
{
    public function saveArticles(Collection $articles, string $sourceName): int
    {
        // Resolve the source once for the whole batch — all articles from one provider share it.
        $source = Source::firstOrCreate(['name' => $sourceName]);

        $saved = 0;

        foreach ($articles as $data) {
            // Each article is its own transaction so one bad row doesn't abort the whole batch.
            DB::transaction(function () use ($data, $source, &$saved) {
                $category = !empty($data['category'])
                    ? Category::firstOrCreate(['name' => $data['category']])
                    : null;

                $article = Article::updateOrCreate(
                    // (source_id, url) is the de-dup key — same article from the same source won't insert twice.
                    ['source_id' => $source->id, 'url' => $data['url']],
                    [
                        'category_id'  => $category?->id,
                        'title'        => $data['title'],
                        'description'  => $data['description'] ?? null,
                        'published_at' => $this->parseDate($data['published_at'] ?? null),
                    ]
                );

                $authorIds = collect($data['authors'] ?? [])
                    ->filter()
                    ->unique()
                    ->map(fn (string $name) => Author::firstOrCreate(['name' => $name])->id)
                    ->all();

                $article->authors()->sync($authorIds);

                $saved++;
            });
        }

        return $saved;
    }

    private function parseDate(?string $date): Carbon
    {
        if (!$date) {
            return Carbon::now();
        }

        try {
            return Carbon::parse($date);
        } catch (\Throwable) {
            return Carbon::now();
        }
    }
}
