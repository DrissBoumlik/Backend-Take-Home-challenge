<?php

namespace App\Services;

use App\Models\Article;

class ArticleService
{

    public function __construct(public NewsAggregatorService $newsAggregatorService)
    {

    }

    public function fetchArticlesFromAPIs(): void
    {
        $this->newsAggregatorService->importArticles();
    }
}
