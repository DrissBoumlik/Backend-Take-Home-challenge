<?php

namespace App\Services;

use App\Config\PaginationConfig;
use App\Exceptions\UserPreferenceNotFoundException;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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

    /**
     * @throws \Exception
     */
    public function searchArticles(string $term, int $perPage): LengthAwarePaginator
    {
        return $this->articleSearchService->search($term)->paginate($this->getPerPage($perPage));
    }

    public function filterArticles(array $filters, $perPage): LengthAwarePaginator
    {
        return $this->articleFilterService->filter($filters)->paginate($this->getPerPage($perPage));
    }

    /**
     * @throws Throwable
     * @throws AuthenticationException
     * @throws UserPreferenceNotFoundException
     */
    public function getArticlesByPreferences(int $perPage): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = Auth::user();

            if (! $user) {
                throw new AuthenticationException('Unauthenticated user');
            }

            $userPreferences = UserPreference::where('user_id', $user->id)->first();

            if (! $userPreferences) {
                throw new UserPreferenceNotFoundException('User preferences not found', 404);
            }

            $query = Article::query();

            $this->applyFilters($query, $userPreferences);

            $articles = $query->latest('published_at')->paginate($this->getPerPage($perPage));

            return ArticleResource::collection($articles);

        } catch (UserPreferenceNotFoundException $e) {
            Log::warning('User preferences not found: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);

            return response()->json(['message' => 'User preferences not found' ], Response::HTTP_NOT_FOUND);
        } catch (AuthenticationException $e) {
            Log::warning('Unauthenticated user: ' . $e->getMessage(), [ 'user_id' => auth()->id() ]);

            return response()->json(['message' => 'Unauthenticated user', ], Response::HTTP_UNAUTHORIZED);
        } catch (Throwable $e) {
            Log::error('Error in getting articles by preferences: ' . $e->getMessage(), ['exception' => $e]);

            throw new \Exception("Failed to fetch articles.");
        }
    }

    private function applyFilters(Builder $query, UserPreference $userPreferences): void
    {
        if ($userPreferences->sources) {
            $query->whereIn('source', $userPreferences->sources);
        }

        if ($userPreferences->categories) {
            $query->whereIn('category', $userPreferences->categories);
        }

        if ($userPreferences->authors) {
            $query->whereIn('author', $userPreferences->authors);
        }
    }
}
