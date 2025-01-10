<?php

namespace App\Services\NewsApis;

use App\Contracts\ApiSource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NYTimesService implements ApiSource
{

    private string $url;
    private string $apiKey;
    private string $name;
    private mixed $articles;

    public function __construct()
    {
        $this->url = config('news-sources.ny_times.config.url');
        $this->apiKey = config('news-sources.ny_times.config.apikey');
        $this->name = config('news-sources.ny_times.config.source');
    }

    /**
     * @throws \Throwable
     */
    public function fetchArticles(): self
    {

        try {
            $response = Http::get($this->url, [
                'api-key' => $this->apiKey,
            ]);
            if ($response->failed()) {
                Log::error('Failed to fetch articles from New York Times', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to fetch articles from New York Times');
            }
            $this->articles = $response->json('results', []);
        } catch (\Throwable $e) {
            Log::error("Error fetching articles from New York Times");
        }

        return $this;
    }

    public function parse(): array
    {
        $articles = [];

        foreach ($this->articles as $article) {
            $articles[] = [
                'title' => $article['title'],
                'content' => $article['abstract'] ?? "Content not available",
                'author' => $article['byline'] ?? "Unknown Author",
                'source' => 'New York Times',
                'category' => $article['section'] ?? "Uncategorized",
                'published_at' => $article['published_date'] ? Carbon::parse($article['published_date'])->format('Y-m-d H:i:s') : now(),
            ];
        }

        return $articles;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
