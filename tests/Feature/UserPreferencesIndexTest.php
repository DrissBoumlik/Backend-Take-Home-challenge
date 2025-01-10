<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPreference;
use App\Services\Article\ArticleService;
use App\Services\User\UserPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserPreferencesIndexTest extends TestCase
{

    use RefreshDatabase;

    public function test_index_returns_user_preferences(): void
    {
        $user = User::factory()->create();
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'sources' => ['Source A', 'Source B'],
            'categories' => ['Technology', 'Finance'],
            'authors' => ['Author X', 'Author Y'],
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/api/user/preferences');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        "sources",
                        "categories",
                        "authors",
                    ],
                ],
            ]);
    }

    public function test_index_throw_exception(): void
    {
        $mockSearchService = \Mockery::mock(UserPreferenceService::class);
        $mockSearchService->allows('getPreferences')
            ->andThrow(new \Exception('Failed retrieve user preferences'));

        $this->app->instance(ArticleService::class, $mockSearchService);

        $response = $this->getJson('/api/user/preferences');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'message' => 'Failed to retrieve user preferences'
        ]);
    }
}
