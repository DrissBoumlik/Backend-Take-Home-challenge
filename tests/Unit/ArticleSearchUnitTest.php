<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Services\ArticleFilterService;
use App\Services\ArticleSearchService;
use App\Services\ArticleService;
use App\Services\NewsAggregatorService;
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
        );
    }

    public function test_search_builds_correct_query()
    {
        Article::factory()->create(['title' => 'First Testing Article']);
        Article::factory()->create(['title' => 'Second Testing Article']);
        Article::factory()->create(['title' => 'Unrelated Title']);

        $term = 'Testing';

        $results = $this->articleService->searchArticles($term, 10);

        $this->assertCount(2, $results);
        $this->assertEquals('First Testing Article', $results[0]->title);
        $this->assertEquals('Second Testing Article', $results[1]->title);
    }

    public function test_search_handles_empty_results()
    {
        Article::factory()->create(['title' => 'No Match']);

        $term = 'Nonexistent';

        $results = $this->articleService->searchArticles($term, 10);

        $this->assertCount(0, $results);
    }

    public function test_search_handles_exceptions()
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
