<?php

namespace Tests\Unit;

use Domain\Articles\Models\Article;
use Domain\Articles\Services\ArticleFilterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleFilterUnitTest extends TestCase
{
    use RefreshDatabase;

    private ArticleFilterService $articleFilterService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleFilterService = new ArticleFilterService();
    }

    public function test_filters_by_category(): void
    {
        Article::factory()->create(['category' => 'Tech']);
        Article::factory()->create(['category' => 'Health']);

        $filters = ['category' => 'Tech'];
        $query = $this->articleFilterService->filter($filters);

        $this->assertCount(1, $query->get());
        $this->assertEquals('Tech', $query->first()->category);
    }

    public function test_filters_by_date_range(): void
    {
        Article::factory()->create(['published_at' => '2025-01-01']);
        Article::factory()->create(['published_at' => '2025-01-03']);
        Article::factory()->create(['published_at' => '2025-01-05']);
        Article::factory()->create(['published_at' => '2025-01-10']);

        $filters = ['start_date' => '2025-01-01', 'end_date' => '2025-01-05'];
        $query = $this->articleFilterService->filter($filters);
        $articles = $query->get();

        $this->assertCount(3, $articles);
        $this->assertEquals('2025-01-05', $query->first()->published_at->toDateString());
    }

    public function test_filters_by_source(): void
    {
        Article::factory()->create(['source' => 'TechCrunch']);
        Article::factory()->create(['source' => 'HealthLine']);

        $filters = ['source' => 'TechCrunch'];
        $query = $this->articleFilterService->filter($filters);

        $this->assertCount(1, $query->get());
        $this->assertEquals('TechCrunch', $query->first()->source);
    }

    public function test_filters_by_non_existing_source(): void
    {
        Article::factory()->create(['source' => 'TechCrunch']);
        Article::factory()->create(['source' => 'HealthLine']);

        $filters = ['source' => 'NonExistingSource'];
        $query = $this->articleFilterService->filter($filters);

        $this->assertCount(0, $query->get());
    }
}
