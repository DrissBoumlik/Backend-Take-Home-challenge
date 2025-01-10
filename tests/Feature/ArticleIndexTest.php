<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Services\Article\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArticleIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_articles(): void
    {
        Article::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/articles?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'title',
                        'content',
                        'author',
                        'source',
                        'category',
                        'published_at',
                    ],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'last_page', 'per_page', 'to', 'total'],
            ])
            ->assertJsonFragment(['per_page' => 10]);
    }

    public function test_index_throw_exception(): void
    {
        $mockArticleService = \Mockery::mock(ArticleService::class);
        $mockArticleService->allows('getAllArticles')
            ->andThrow(new \Exception('Error fetching articles'));

        $this->app->instance(ArticleService::class, $mockArticleService);

        $response = $this->getJson('/api/v1/articles');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'message' => 'Failed to retrieve articles'
        ]);
    }

    public function test_inedx_invalidate_per_page_parameter(): void
    {
        Article::factory()->create(['title' => 'Article Title']);

        $response = $this->getJson('/api/v1/articles?per_page=0');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('errors.per_page.0', 'The per page field must be at least 1.');
    }

}
