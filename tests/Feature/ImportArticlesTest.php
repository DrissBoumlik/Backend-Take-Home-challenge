<?php

namespace Tests\Feature;

use Domain\NewsApis\Services\DataSourceManager;
use Domain\NewsApis\Services\NewsAggregatorService;
use Domain\NewsApis\Services\Sources\GuardianService;
use Domain\NewsApis\Services\Sources\NewsApiService;
use Domain\NewsApis\Services\Sources\NYTimesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class ImportArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_and_store_articles_from_source(): void
    {
        Http::fake([
            'https://newsapi.org/*' => Http::response([
                'articles' => [
                    [
                        'title' => 'Sample Article',
                        'description' => 'This is a sample article.',
                        'author' => 'John Doe',
                        'category' => 'Tech',
                        'publishedAt' => now(),
                    ],
                ],
            ], 200),
        ]);

        // Create an instance of ApiFetcher
        $fetcher = app(NewsAggregatorService::class);

        $fetcher->importArticles();

        $this->assertDatabaseHas('articles', [
            'title' => 'Sample Article',
            'author' => 'John Doe',
            'category' => 'Tech',
        ]);
    }

    public function test_fetch_articles_from_source_fails(): void
    {
        $mockDataSourceManager = Mockery::mock(DataSourceManager::class);
        $mockDataSourceManager->allows('getDataSources')
            ->andReturn([NewsApiService::class, GuardianService::class]);

        $this->app->instance(DataSourceManager::class, $mockDataSourceManager);

        $fakeSource1 = Mockery::mock(NewsApiService::class);
        $fakeSource1->allows('fetchArticles')->andThrow(new \Exception('Source 1 failed'));

        $fakeSource2 = Mockery::mock(GuardianService::class);
        $fakeSource2->allows('fetchArticles')->andThrow(new \Exception('Source 2 failed'));

        $this->app->instance(NewsApiService::class, $fakeSource1);
        $this->app->instance(GuardianService::class, $fakeSource2);

        $fetcher = app(NewsAggregatorService::class);

        $fetcher->importArticles();

        $this->assertDatabaseCount('articles', 0);
    }

    public function test_fetch_articles_handles_failed_response()
    {
        config()->set('news-sources.newsapi.config.url', 'https://mock-newsapi.org/v2/top-headlines');
        config()->set('news-sources.guardian.config.url', 'https://mock-content.guardianapis.com/search');
        config()->set('news-sources.ny_times.config.url', 'https://mock-api.nytimes.com/svc/topstories/v2/home.json');

        $sources = [
            NewsApiService::class,
            GuardianService::class,
            NYTimesService::class
        ];

        foreach ($sources as $source) {
            $mockService = \Mockery::mock($source);
            $mockService->allows('fetchArticles')
                ->andThrow(new \Exception('Failed to fetch articles.'));

            $this->app->instance($source, $mockService);

            $sourceClass = new $source;
            $sourceClass->fetchArticles();

            $this->assertEmpty($sourceClass->getArticles());
        }
    }


}
