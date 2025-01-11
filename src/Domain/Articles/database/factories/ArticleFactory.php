<?php

namespace Domain\Articles\database\factories;

use Domain\Articles\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Articles\Models\Article>
 */
class ArticleFactory extends Factory
{

    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title(),
            'content' => fake()->text(),
            'author' => fake()->name(),
            'source' => fake()->randomElement(["The Guardian", "NewsAPI", "New York Times"]),
            'category' => fake()->randomElement(["weather", "history"]),
            'published_at' => fake()->dateTimeBetween("-2 years", now()->addDays(-1)),
        ];
    }
}
