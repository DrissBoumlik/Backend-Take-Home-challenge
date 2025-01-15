<?php

namespace Domain\NewsApis\Services\Sources;

use Carbon\Carbon;

class NewsApiService extends SourceService
{
    /**
     * @param array<string, mixed> $extraQueryParameters
     */
    public function setConfig(): self
    {
        $this->url = config('news-sources.newsapi.config.url');
        $this->apiKey = config('news-sources.newsapi.config.apikey');
        $this->name = config('news-sources.newsapi.config.source');

        $this->queryParameters = [
            'apiKey' => $this->apiKey,
            "country" => "us",
        ];

        $this->responseArticlesKeyAccess = 'articles';

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
                'published_at' => $article['publishedAt'] ? Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') : now(),
            ];
        }

        return $articles;
    }

}
