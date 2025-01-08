<?php

namespace App\Services;

use App\Contracts\ApiSource;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{

    public function __construct(public DataSourceManager $dataSourceManager)
    {

    }

    public function importArticles(): void
    {
        $dataSources = $this->dataSourceManager->getDataSources();

        foreach ($dataSources as $sourceClass) {
            try {
                $source = app($sourceClass);
                $this->importArticlesFromSource($source);
            } catch (\Throwable $e) {
                Log::error("Failed to import articles from source", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    public function importArticlesFromSource(ApiSource $source): void
    {
        $fetchedArticles = $source->fetchArticles()->parse();

        $this->insertArticles($fetchedArticles);
    }

    private function insertArticles($articles): void
    {
        Article::insert($articles);
    }
}
