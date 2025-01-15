<?php

namespace Domain\NewsApis\Services\Sources;

use Carbon\Carbon;

class NYTimesService extends SourceService
{
    /**
     * @param array<string, mixed> $extraQueryParameters
     */
    public function setConfig(array $extraQueryParameters = []): self
    {
        $this->url = config('news-sources.ny_times.config.url');
        $this->apiKey = config('news-sources.ny_times.config.apikey');
        $this->name = config('news-sources.ny_times.config.source');

        $this->queryParameters = [
            'api-key' => $this->apiKey,
        ];

        $this->responseArticlesKeyAccess = 'results';

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

}
