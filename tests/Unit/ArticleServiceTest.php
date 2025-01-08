<?php

namespace Tests\Unit;

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
}
