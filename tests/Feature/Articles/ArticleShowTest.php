<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_single_article(): void
    {
        $article = Article::factory()->create();

        $this->getJson("/api/articles/{$article->id}")
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'url', 'published_at', 'source', 'category', 'authors'],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $article->id);
    }

    public function test_returns_404_for_missing_article(): void
    {
        $this->getJson('/api/articles/99999')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }
}
