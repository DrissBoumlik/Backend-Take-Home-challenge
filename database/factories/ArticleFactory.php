<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
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
