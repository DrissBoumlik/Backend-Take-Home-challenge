<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Services\ArticleService;
use App\Services\NewsAggregatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{

    use RefreshDatabase;

    public function test_fetch_articles_from_apis(): void
    {
        $apiFetcherMock = \Mockery::mock(NewsAggregatorService::class);
        $apiFetcherMock->expects('importArticles');

        $this->app->instance(NewsAggregatorService::class, $apiFetcherMock);

        $service = app(ArticleService::class);

        $service->fetchArticlesFromAPIs();

        $apiFetcherMock->shouldHaveReceived('importArticles');
    }


    public function test_get_all_articles(): void
    {
        Article::factory()->count(3)->create();

        $service = app(ArticleService::class);

        $articles = $service->getAllArticles();

        $this->assertCount(3, $articles);
        $this->assertArrayHasKey('title', $articles->first()->toArray());
        $this->assertArrayHasKey('content', $articles->first()->toArray());
        $this->assertArrayHasKey('author', $articles->first()->toArray());
        $this->assertArrayHasKey('source', $articles->first()->toArray());
        $this->assertArrayHasKey('category', $articles->first()->toArray());
        $this->assertArrayHasKey('published_at', $articles->first()->toArray());
    }
}
