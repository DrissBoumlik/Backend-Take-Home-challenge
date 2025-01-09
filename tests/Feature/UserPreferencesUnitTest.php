<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserPreferencesUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_articles_by_preferences_with_valid_preferences()
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

    public function test_get_articles_by_preferences_without_preferences()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson('/api/articles/preferences');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No preferences found for the user']);
    }
}
