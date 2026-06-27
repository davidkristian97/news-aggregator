<?php

namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'source_id' => Source::factory(),
            'category_id' => null,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'url' => fake()->unique()->url(),
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
