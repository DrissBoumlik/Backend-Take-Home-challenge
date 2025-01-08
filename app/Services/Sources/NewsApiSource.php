<?php

namespace App\Services\Sources;

use App\Contracts\ApiSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiSource implements ApiSource
{
    private string $url;
    private string $apiKey;
    private string $name;
    private mixed $articles;

    public function __construct()
    {
        $this->url = config('news-sources.newsapi.config.url');
        $this->apiKey = config('news-sources.newsapi.config.apikey');
        $this->name = config('news-sources.newsapi.config.source');
    }

    /**
     * @throws \Throwable
     */
    public function fetchArticles(string $country = 'us'): self
    {

        try {
            $response = Http::get($this->url, [
                'apiKey' => $this->apiKey,
                'country' => $country,
            ]);
            if ($response->failed()) {
                Log::error('Failed to fetch articles from NewsAPI', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to fetch articles from NewsAPI');
            }
            $this->articles = $response->json('articles', []);
        } catch (\Throwable $e) {
            Log::error("Error fetching articles from NewsAPI");
        }

        return $this;
    }

    public function parse(): array
    {
        $articles = [];

        foreach ($this->articles as $article) {
            $articles[] = [
                'title' => $article['title'],
                'content' => $article['description'] ?? "Content not available",
                'author' => $article['author'] ?? "Unknown Author",
                'source' => 'NewsAPI',
                'category' => $article['category'] ?? "Uncategorized",
                'published_at' => $article['publishedAt'] ?? now(),
            ];
        }

        return $articles;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
