<?php

namespace App\Services;

use App\Config\PaginationConfig;
use App\Exceptions\UserPreferenceNotFoundException;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

class ArticleService
{

    public function __construct(
        public NewsAggregatorService $newsAggregatorService,
        public ArticleSearchService $articleSearchService,
        public ArticleFilterService $articleFilterService
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

    public function filterArticles(array $filters, $perPage): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->articleFilterService->filter($filters)->paginate($this->getPerPage($perPage));
    }

    /**
     * @throws UserPreferenceNotFoundException
     */
    public function getArticlesByPreferences(int $perPage): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $user = Auth::user();

        $preferences = UserPreference::where('user_id', $user->id)->first();

        // $preferences = UserPreference::inRandomOrder()->first(); // testing purposes

        if (! $preferences) {
            throw new UserPreferenceNotFoundException('No preferences found for the user', 404);
        }

        $query = Article::query();

        if ($preferences->sources) {
            $query->whereIn('source', $preferences->sources);
        }

        if ($preferences->categories) {
            $query->whereIn('category', $preferences->categories);
        }

        if ($preferences->authors) {
            $query->whereIn('author', $preferences->authors);
        }
        /*
         * whereIn()->whereIn() or whereIn()->orWhereIn() will depend on the requirements
         */

        return $query->latest('published_at')->paginate($this->getPerPage($perPage));
    }
}
