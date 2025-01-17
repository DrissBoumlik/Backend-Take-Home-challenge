<?php

namespace Tests\Unit;

use Domain\Articles\Models\Article;
use Domain\Articles\Services\ArticleSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleSearchUnitTest extends TestCase
{
    use RefreshDatabase;

    protected ArticleSearchService $articleSearchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleSearchService = new ArticleSearchService;
    }

    public function test_search_builds_correct_query(): void
    {
        Article::factory()->create(['title' => 'First Testing Article', 'published_at' => now()->subDays(2)]);
        Article::factory()->create(['title' => 'Second Testing Article', 'published_at' => now()]);
        Article::factory()->create(['title' => 'Unrelated Title']);

        $term = 'Testing';

        $results = $this->articleSearchService->search($term)->get();

        $this->assertCount(2, $results);
        $this->assertEquals('First Testing Article', $results[1]->title);
        $this->assertEquals('Second Testing Article', $results[0]->title);
    }

    public function test_search_handles_empty_results(): void
    {
        Article::factory()->create(['title' => 'No Match']);

        $term = 'Nonexistent';

        $results = $this->articleSearchService->search($term)->get();

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
