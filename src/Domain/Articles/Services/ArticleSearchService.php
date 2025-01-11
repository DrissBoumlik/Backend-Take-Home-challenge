<?php

namespace Domain\Articles\Services;

use App\Contracts\SearchServiceInterface;
use Domain\Articles\Models\Article;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ArticleSearchService implements SearchServiceInterface
{
    protected array $searchableFields = [
        'title',
        'content',
        'author',
        'category',
    ];

    /**
     * @throws Exception
     */
    public function search(string $term): Builder
    {
        try {
            $searchQuery = Article::query();
            return $this->buildSearchQuery($searchQuery, $term);
        } catch (\Throwable $e) {
            Log::error('Search failed: ' . $e->getMessage(), ['term' => $term]);
            throw new Exception('Failed to perform search: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function buildSearchQuery(Builder $query, string $term): Builder
    {
        $term = "%" . preg_replace('/[^A-Za-z0-9]/', '', $term) . "%";
        $query->where(function ($q) use ($term): void {
            foreach ($this->searchableFields as $field) {
                $q->orWhereRaw("regexp_replace($field, '[^A-Za-z0-9]', '') like ?", [$term]);
                // The regexp_replace do not exist in sqlite, but it was registered in the AppServiceProvider
            }
        });

        return $query->latest('published_at');
    }
}
