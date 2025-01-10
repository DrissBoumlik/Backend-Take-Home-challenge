<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Services\ArticleFilterService;
use App\Services\ArticleSearchService;
use App\Services\ArticleService;
use App\Services\NewsAggregatorService;
use App\Services\UserPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleSearchUnitTest extends TestCase
{
    use RefreshDatabase;

    protected ArticleService $articleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleService = new ArticleService(
            \Mockery::mock(NewsAggregatorService::class),
            new ArticleSearchService,
            \Mockery::mock(ArticleFilterService::class),
            \Mockery::mock(UserPreferenceService::class),
        );
    }

    public function test_search_builds_correct_query(): void
    {
        Article::factory()->create(['title' => 'First Testing Article', 'published_at' => now()->subDays(2)]);
        Article::factory()->create(['title' => 'Second Testing Article', 'published_at' => now()]);
        Article::factory()->create(['title' => 'Unrelated Title']);

        $term = 'Testing';

        $results = $this->articleService->searchArticles($term, 10);

        $this->assertCount(2, $results);
        $this->assertEquals('First Testing Article', $results[1]->title);
        $this->assertEquals('Second Testing Article', $results[0]->title);
    }

    public function test_search_handles_empty_results(): void
    {
        Article::factory()->create(['title' => 'No Match']);

        $term = 'Nonexistent';

        $results = $this->articleService->searchArticles($term, 10);

        $this->assertCount(0, $results);
    }

    public function test_search_handles_exceptions(): void
    {
        $mockService = $this->getMockBuilder(ArticleSearchService::class)
            ->onlyMethods(['buildSearchQuery'])
            ->getMock();

        $mockService->method('buildSearchQuery')->willThrowException(new \Exception('Mocked exception'));

        $this->expectException(\Throwable::class);
        $this->expectExceptionMessage('Failed to perform search: Mocked exception');

        $mockService->search('term');
    }
}
