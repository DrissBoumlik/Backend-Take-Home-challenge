<?php

namespace Tests\Feature;

use Domain\Articles\Models\Article;
use Domain\Articles\Services\ArticleFilterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArticleFilterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_articles_by_category(): void
    {
        Article::factory()->create(['category' => 'Tech']);
        Article::factory()->create(['category' => 'Health']);

        $response = $this->getJson('/api/v1/articles/filter?category=Tech');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', 'Tech');
    }

    public function test_filter_articles_by_date_range(): void
    {
        Article::factory()->create(['published_at' => '2025-01-01']);
        Article::factory()->create(['published_at' => '2025-01-10']);

        $response = $this->getJson('/api/v1/articles/filter?start_date=2025-01-01&end_date=2025-01-05');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.published_at', '2025-01-01T00:00:00.000000Z');
    }

    public function test_filter_articles_with_pagination(): void
    {
        Article::factory(15)->create();

        $response = $this->getJson('/api/v1/articles/filter?per_page=10');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonCount(10, 'data');
    }

    public function test_filter_articles_by_non_existing_source(): void
    {
        Article::factory()->create(['source' => 'Source 1']);
        Article::factory()->create(['source' => 'Source 2']);

        $response = $this->getJson('/api/v1/articles/filter?source=NonExisting');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(0, 'data');
    }


    public function test_filter_throws_a_failure_exception(): void
    {
        $mockFilterService = \Mockery::mock(ArticleFilterService::class);
        $mockFilterService->allows('filter')
            ->andThrow(new \Exception('Something went wrong'));

        $this->app->instance(ArticleFilterService::class, $mockFilterService);

        $response = $this->getJson('/api/v1/articles/filter?source=Source1&category=Sports');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'message' => 'Failed to filter articles'
        ]);
    }

    public function test_filter_invalidate_request_parameters(): void
    {
        Article::factory()->create(['source' => 'Source 1']);
        Article::factory()->create(['source' => 'Source 2']);

        $response = $this->getJson('/api/v1/articles/filter?start_date=NotADate');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('errors.start_date.0', 'The start date field must be a valid date.');
    }

}
