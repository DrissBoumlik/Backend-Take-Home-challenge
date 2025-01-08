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
        $term = "%" . preg_replace('/[^A-Za-z0-9]/', '', $term) . "%";
        $query->where(function ($q) use ($term) {
            foreach ($this->searchableFields as $index => $field) {
                $q->orWhereRaw("regexp_replace($field, '[^A-Za-z0-9]', '') like ?", [$term]);
                // The regexp_replace do not exist in sqlite, but it was registered in the AppServiceProvider
            }
        });

        return $query->latest('published_at');
    }
}
