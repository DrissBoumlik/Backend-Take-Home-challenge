<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_articles_by_category()
    {
        Article::factory()->create(['category' => 'Tech']);
        Article::factory()->create(['category' => 'Health']);

        $response = $this->getJson('/api/articles/filter?category=Tech');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', 'Tech');
    }

    public function test_filter_articles_by_date_range()
    {
        Article::factory()->create(['published_at' => '2025-01-01']);
        Article::factory()->create(['published_at' => '2025-01-10']);

        $response = $this->getJson('/api/articles/filter?start_date=2025-01-01&end_date=2025-01-05');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.published_at', '2025-01-01T00:00:00.000000Z');
    }

    public function test_filter_articles_with_pagination()
    {
        Article::factory(15)->create();

        $response = $this->getJson('/api/articles/filter?per_page=10');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonCount(10, 'data');
    }
}
