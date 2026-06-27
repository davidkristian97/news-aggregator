<?php

namespace Tests\Feature\Me;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_preferences_require_auth(): void
    {
        $this->getJson('/api/me/preferences')->assertUnauthorized();
        $this->putJson('/api/me/preferences', [])->assertUnauthorized();
    }

    public function test_get_preferences(): void
    {
        $user = User::factory()->create();
        $source = Source::factory()->create();
        $user->sources()->attach($source->id);

        Sanctum::actingAs($user);

        $this->getJson('/api/me/preferences')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['sources', 'categories', 'authors'],
            ])
            ->assertJsonCount(1, 'data.sources');
    }

    public function test_update_preferences(): void
    {
        $user = User::factory()->create();
        $source = Source::factory()->create();
        $category = Category::factory()->create();
        $author = Author::factory()->create();

        Sanctum::actingAs($user);

        $this->putJson('/api/me/preferences', [
            'source_ids' => [$source->id],
            'category_ids' => [$category->id],
            'author_ids' => [$author->id],
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.sources')
            ->assertJsonCount(1, 'data.categories')
            ->assertJsonCount(1, 'data.authors');

        $this->assertDatabaseHas('user_preferred_sources', ['user_id' => $user->id, 'source_id' => $source->id]);
    }

    public function test_update_preferences_rejects_invalid_ids(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->putJson('/api/me/preferences', ['source_ids' => [99999]])
            ->assertUnprocessable()
            ->assertJsonPath('success', false);
    }
}
