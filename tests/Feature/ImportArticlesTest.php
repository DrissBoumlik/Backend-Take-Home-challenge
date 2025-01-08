<?php

namespace Tests\Feature;

use App\Services\NewsAggregatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImportArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_and_store_articles_from_source(): void
    {
        // Mock the API response
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
}
