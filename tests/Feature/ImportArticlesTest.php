<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Services\DataSourceManager;
use App\Services\NewsAggregatorService;
use App\Services\NewsApis\GuardianService;
use App\Services\NewsApis\NewsApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

}
