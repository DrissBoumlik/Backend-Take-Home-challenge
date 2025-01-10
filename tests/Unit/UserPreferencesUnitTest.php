<?php

namespace Tests\Unit;

use App\Exceptions\UserPreferenceNotFoundException;
use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use App\Services\ArticleFilterService;
use App\Services\ArticleSearchService;
use App\Services\ArticleService;
use App\Services\NewsAggregatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserPreferencesUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_articles_by_preferences_returns_correct_data()
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
        );

        $this->actingAs($user);
        $result = $service->getArticlesByPreferences(10);

        $this->assertCount(1, $result->items());
    }

    public function test_get_articles_by_preferences_throws_exception_without_preferences()
    {
        $user = User::factory()->create();
        $service = new ArticleService(
            \Mockery::mock(NewsAggregatorService::class),
            \Mockery::mock(ArticleSearchService::class),
            \Mockery::mock(ArticleFilterService::class),
        );

        $this->actingAs($user);
        $response = $service->getArticlesByPreferences(10);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
