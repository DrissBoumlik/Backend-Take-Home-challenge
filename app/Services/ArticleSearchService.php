<?php

namespace App\Services;

use App\Contracts\SearchServiceInterface;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Exception;

class ArticleSearchService implements SearchServiceInterface
{
    protected array $searchableFields = [
        'title',
        'content',
        'author',
        'category',
    ];
    private const PER_PAGE = 10;

    /**
     * @throws \Exception
     */
    public function search(string $term, int $perPage = self::PER_PAGE): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        try {
            $searchQuery = Article::query();
            return $this->buildSearchQuery($searchQuery, $term)->paginate($perPage);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to perform search: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function buildSearchQuery(Builder $query, string $term): Builder
    {
        $term = "%$term%";
        $query->where(function ($q) use ($term) {
            foreach ($this->searchableFields as $index => $field) {
                $q->orWhereRaw("$field like ?", [$term]);
            }
        });

        return $query->latest('published_at');
    }
}
