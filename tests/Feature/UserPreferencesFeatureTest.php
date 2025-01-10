<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use App\Services\Article\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserPreferencesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_articles_by_preferences_with_valid_preferences(): void
    {
        $user = User::factory()->create();
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'sources' => ['Source A', 'Source B'],
            'categories' => ['Technology', 'Finance'],
            'authors' => ['Author X', 'Author Y'],
        ]);

        Article::factory()->create(['source' => 'Source A', 'category' => 'Technology', 'author' => 'Author X']);
        Article::factory()->create(['source' => 'Source B', 'category' => 'Finance', 'author' => 'Author Y']);
        Article::factory()->create(['source' => 'Source C']);

        $this->actingAs($user);
        $response = $this->getJson('/api/articles/preferences');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_get_articles_by_preferences_without_preferences(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson('/api/articles/preferences');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['message' => 'User preferences not found']);
    }

    public function test_get_articles_by_preferences_for_unauthenticated_user(): void
    {
        $response = $this->getJson('/api/articles/preferences');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated user']);
    }

    public function test_filter_by_user_preferences_throws_exception(): void
    {
        $mockSearchService = \Mockery::mock(ArticleService::class);
        $mockSearchService->allows('getArticlesByPreferences')
            ->andThrow(new \Exception('Failed to fetch articles'));

        $this->app->instance(ArticleService::class, $mockSearchService);

        $response = $this->getJson('/api/articles/preferences');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'message' => 'An unexpected error occurred. Please try again later.'
        ]);
    }

    public function test_filter_by_user_preferences_throws_user_preferences_not_found_exception(): void
    {

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson('/api/articles/preferences');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'message' => 'User preferences not found'
        ]);
    }
}
