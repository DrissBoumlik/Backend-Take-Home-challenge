<?php

namespace Tests\Feature;

use Domain\Articles\Models\Article;
use Domain\Articles\Services\ArticleService;
use Domain\Users\Models\User;
use Domain\Users\Models\UserPreference;
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
        $response = $this->getJson('/api/v1/articles/preferences');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_get_articles_by_preferences_without_preferences(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson('/api/v1/articles/preferences');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['message' => 'User preferences not found']);
    }

    public function test_get_articles_by_preferences_for_unauthenticated_user(): void
    {
        $response = $this->getJson('/api/v1/articles/preferences');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated user']);
    }

    public function test_filter_by_user_preferences_throws_exception(): void
    {
        $mockArticleService = \Mockery::mock(ArticleService::class);
        $mockArticleService->allows('getArticlesByUserPreferences')
            ->andThrow(new \Exception('Failed to fetch articles'));

        $this->app->instance(ArticleService::class, $mockArticleService);

        $response = $this->getJson('/api/v1/articles/preferences');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'message' => 'An unexpected error occurred. Please try again later.'
        ]);
    }
}
