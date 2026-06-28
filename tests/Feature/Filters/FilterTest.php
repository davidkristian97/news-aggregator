<?php

namespace Tests\Feature\Filters;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_get_all_categories(): void
    {
        Category::factory(3)->create();

        $this->getJson('/api/filters/categories')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_get_all_sources_without_query(): void
    {
        Source::factory(3)->create();

        $this->getJson('/api/filters/sources')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_search_sources_returns_matches(): void
    {
        Source::factory()->create(['name' => 'NYT']);
        Source::factory()->create(['name' => 'Guardian']);

        $this->getJson('/api/filters/sources?q=NYT')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'NYT');
    }

    public function test_get_all_authors_without_query(): void
    {
        Author::factory(3)->create();

        $this->getJson('/api/filters/authors')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_search_authors_returns_matches(): void
    {
        Author::factory()->create(['name' => 'Jane']);
        Author::factory()->create(['name' => 'James']);

        $this->getJson('/api/filters/authors?q=Jane')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Jane');
    }
}
