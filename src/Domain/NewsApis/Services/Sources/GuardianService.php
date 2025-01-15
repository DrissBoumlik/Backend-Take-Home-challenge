<?php

namespace Domain\NewsApis\Services\Sources;

use Carbon\Carbon;

class GuardianService extends SourceService
{
    /**
     * @param array<string, mixed> $extraQueryParameters
     */
    public function setConfig(): self
    {
        $this->url = config('news-sources.guardian.config.url');
        $this->apiKey = config('news-sources.guardian.config.apikey');
        $this->name = config('news-sources.guardian.config.source');

        $this->queryParameters = [
            'api-key' => $this->apiKey,
        ];

        $this->responseArticlesKeyAccess = 'response.results';

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

}
