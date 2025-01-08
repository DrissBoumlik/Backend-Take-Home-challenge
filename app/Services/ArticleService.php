<?php

namespace App\Services;

use App\Models\Article;

class ArticleService
{

    private const PER_PAGE = 10;

    public function __construct(public NewsAggregatorService $newsAggregatorService,
                                public ArticleSearchService $articleSearchService)
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

    public function searchArticles(string $term, int $perPage)
    {
        return $this->articleSearchService->search($term, $perPage);
    }
}
