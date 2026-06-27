<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_paginated_articles(): void
    {
        Article::factory(3)->create();

        $this->getJson('/api/articles')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => ['current_page', 'per_page', 'total_pages', 'total_items', 'links'],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_by_source(): void
    {
        $source = Source::factory()->create();
        Article::factory(2)->create(['source_id' => $source->id]);
        Article::factory(3)->create();

        $this->getJson("/api/articles?source_ids[]={$source->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_can_filter_by_category(): void
    {
        $category = Category::factory()->create();
        Article::factory(2)->create(['category_id' => $category->id]);
        Article::factory(3)->create();

        $this->getJson("/api/articles?category_ids[]={$category->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_can_search_by_keyword(): void
    {
        Article::factory()->create(['title' => 'World Cup Sports']);
        Article::factory(3)->create();

        $this->getJson('/api/articles?q=Sports')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_preferences_boost_articles_to_top(): void
    {
        $preferredSource = Source::factory()->create();
        $otherSource = Source::factory()->create();

        $preferredArticle = Article::factory()->create([
            'source_id' => $preferredSource->id,
            'published_at' => now()->subMinutes(10),
        ]);
        Article::factory()->create([
            'source_id' => $otherSource->id,
            'published_at' => now()->subMinutes(5),
        ]);

        $user = User::factory()->create();
        $user->sources()->attach($preferredSource->id);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/articles')->assertOk();

        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.id', $preferredArticle->id);
    }
}
