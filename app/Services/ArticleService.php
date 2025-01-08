<?php

namespace App\Services;

use App\Config\PaginationConfig;
use App\Models\Article;

class ArticleService
{

    public function __construct(
        public NewsAggregatorService $newsAggregatorService,
        public ArticleSearchService $articleSearchService
    ) {

    }

    private function getPerPage(?int $perPage): int
    {
        return PaginationConfig::getPerPage($perPage);
    }

    public function fetchArticlesFromAPIs(): void
    {
        $this->newsAggregatorService->importArticles();
    }

    public function getAllArticles($perPage)
    {
        return Article::select([
            'id',
            'title',
            'content',
            'author',
            'source',
            'category',
            'published_at'
        ])->latest('published_at')->paginate($this->getPerPage($perPage));
    }

    public function searchArticles(string $term, int $perPage): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->articleSearchService->search($term)->paginate($this->getPerPage($perPage));
    }
}
