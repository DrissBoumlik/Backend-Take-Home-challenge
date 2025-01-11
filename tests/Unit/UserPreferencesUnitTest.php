<?php

namespace Tests\Unit;

use Domain\Articles\Models\Article;
use Domain\Articles\Services\ArticleFilterService;
use Domain\Articles\Services\ArticlesByUserPreferenceService;
use Domain\Articles\Services\ArticleSearchService;
use Domain\Articles\Services\ArticleService;
use Domain\NewsApis\Services\NewsAggregatorService;
use Domain\Users\Models\User;
use Domain\Users\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserPreferencesUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_articles_by_preferences_returns_correct_data(): void
    {
        $user = User::factory()->create();
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'sources' => ['Source A'],
            'categories' => ['Technology'],
            'authors' => ['Author X'],
        ]);

        Article::factory()->create(['source' => 'Source A', 'category' => 'Technology', 'author' => 'Author X']);
        Article::factory()->create(['source' => 'Source B']);

        $service = new ArticleService(
            \Mockery::mock(NewsAggregatorService::class),
            \Mockery::mock(ArticleSearchService::class),
            \Mockery::mock(ArticleFilterService::class),
            new ArticlesByUserPreferenceService
        );

        $this->actingAs($user);
        $result = $service->getArticlesByUserPreferences(10);

        $this->assertCount(1, $result->items());
    }

    public function test_get_articles_by_preferences_throws_exception_without_preferences(): void
    {
        $user = User::factory()->create();
        $service = new ArticleService(
            \Mockery::mock(NewsAggregatorService::class),
            \Mockery::mock(ArticleSearchService::class),
            \Mockery::mock(ArticleFilterService::class),
            new ArticlesByUserPreferenceService
        );

        $this->actingAs($user);
        $response = $service->getArticlesByUserPreferences(10);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
