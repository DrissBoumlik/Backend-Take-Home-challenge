<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Services\ArticleSearchService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArticleSearchFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_matching_results(): void
    {
        $article1 = Article::factory()->create(['title' => 'Laravel Testing Basics']);
        $article2 = Article::factory()->create(['title' => 'PHP Unit Testing Guide']);
        $article3 = Article::factory()->create(['title' => 'Random Article Content']);

        $term = 'Testing';

        $response = $this->getJson('/api/articles/search?term=' . $term);

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['title' => $article1->title]);
        $response->assertJsonFragment(['title' => $article2->title]);
    }

    public function test_search_handles_no_results_gracefully(): void
    {
        Article::factory()->create(['title' => 'Completely Unrelated Title']);

        $term = 'Nonexistent';

        $response = $this->getJson('/api/articles/search?term=' . $term);

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_search_validate_empty_search_term(): void
    {
        Article::factory()->create(['title' => 'Article Title']);

        $term = '';

        $response = $this->getJson('/api/articles/search?term=' . $term);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('errors.term.0', 'The term field is required.');
    }

    public function test_search_throws_a_search_failure_exception(): void
    {
        $mockSearchService = \Mockery::mock(ArticleSearchService::class);
        $mockSearchService->allows('search')
            ->andThrow(new \Exception('Something went wrong'));

        $this->app->instance(ArticleSearchService::class, $mockSearchService);

        $term = 'test';
        $response = $this->getJson('/api/articles/search?term=' . $term);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'message' => 'Failed to search articles'
        ]);
    }

}
