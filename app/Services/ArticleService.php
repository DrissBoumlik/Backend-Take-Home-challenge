<?php

namespace App\Services;

use App\Models\Article;

class ArticleService
{

    private const PER_PAGE = 10;

    public function __construct(public NewsAggregatorService $newsAggregatorService)
    {

    }

    public function fetchArticlesFromAPIs(): void
    {
        $this->newsAggregatorService->importArticles();
    }

    public function getAllArticles($perPage = self::PER_PAGE)
    {
        return Article::select([
            'id',
            'title',
            'content',
            'author',
            'source',
            'category',
            'published_at'
        ])->latest('published_at')->paginate($perPage);
    }
}
