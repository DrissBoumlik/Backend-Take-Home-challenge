<?php

namespace App\Services\Sources;

use App\Contracts\ApiSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GuardianSource implements ApiSource
{

    private string $url;
    private string $apiKey;
    private string $name;
    private mixed $articles;

    public function __construct()
    {
        $this->url = config('news-sources.guardian.config.url');
        $this->apiKey = config('news-sources.guardian.config.apikey');
        $this->name = config('news-sources.guardian.config.source');
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
                Log::error('Failed to fetch articles from Guardian', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to fetch articles from Guardian');
            }
            $this->articles = $response->json('response.results', []);
        } catch (\Throwable $e) {
            Log::error("Error fetching articles from Guardian");
        }

        return $this;
    }

    public function parse(): array
    {
        $articles = [];

        foreach ($this->articles as $article) {
            $articles[] = [
                'title' => $article['webTitle'],
                'content' => $article['fields']['bodyText'] ?? "Content not available",
                'author' => $article['fields']['byline'] ?? "Unknown Author",
                'source' => 'The Guardian',
                'category' => $article['sectionName'] ?? "Uncategorized",
                'published_at' => $article['webPublicationDate'] ? Carbon::parse($article['webPublicationDate'])->format('Y-m-d H:i:s') : now(),
            ];
        }

        return $articles;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
