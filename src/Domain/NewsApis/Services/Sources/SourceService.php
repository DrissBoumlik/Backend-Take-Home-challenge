<?php

namespace Domain\NewsApis\Services\Sources;

use Domain\NewsApis\Contracts\ApiSource;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class SourceService implements ApiSource
{
    protected string $url;
    protected string $apiKey;
    protected string $name;
    protected array $articles;
    protected array $queryParameters;
    protected string $responseArticlesKeyAccess;

    public function __construct()
    {
        $this->articles = [];
    }

    public function fetchArticles(): ApiSource
    {
        $response = null;
        try {
            $response = Http::get($this->url, $this->queryParameters);
            if ($response->failed()) {
                throw new Exception("Failed to fetch articles from ???");
            }
            $this->articles = $response->json($this->responseArticlesKeyAccess, []);
        } catch (Throwable $e) {
            Log::error("Failed to fetch articles from {$this->getName()}", [
                'status' => $response?->status(),
                'body' => $response?->body(),
            ]);
        }

        return $this;
    }

    public function getArticles(): array
    {
        return $this->articles;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
