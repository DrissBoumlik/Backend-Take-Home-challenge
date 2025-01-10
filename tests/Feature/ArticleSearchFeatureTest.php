<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleSearchFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_matching_results(): void
    {
        $article1 = Article::factory()->create(['title' => 'Laravel Testing Basics']);
        $article2 = Article::factory()->create(['title' => 'PHP Unit Testing Guide']);
        $article3 = Article::factory()->create(['title' => 'Random Article Content']);

        $term = 'Testing';

        $response = $this->getJson('/api/articles/search?term=' . $term);

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['title' => $article1->title]);
        $response->assertJsonFragment(['title' => $article2->title]);
    }

    public function test_search_handles_no_results_gracefully(): void
    {
        Article::factory()->create(['title' => 'Completely Unrelated Title']);

        $term = 'Nonexistent';

        $response = $this->getJson('/api/articles/search?term=' . $term);

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
