<?php

namespace Domain\Articles\Services;

use App\Config\PaginationConfig;
use Domain\Articles\Exceptions\UserPreferenceNotFoundException;
use Domain\Articles\Http\Resources\ArticleResource;
use Domain\Articles\Models\Article;
use Domain\NewsApis\Services\NewsAggregatorService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ArticleService
{

    public function __construct(
        public NewsAggregatorService $newsAggregatorService,
        public ArticleSearchService $articleSearchService,
        public ArticleFilterService $articleFilterService,
        public ArticlesByUserPreferenceService $userPreferenceService
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

    public function getAllArticles($perPage): AnonymousResourceCollection
    {
        $articles = Article::select([
            'id',
            'title',
            'content',
            'author',
            'source',
            'category',
            'published_at'
        ])->latest('published_at')->paginate($this->getPerPage($perPage));
        return ArticleResource::collection($articles);
    }

    /**
     * @throws Exception
     */
    public function searchArticles(string $term, int $perPage): AnonymousResourceCollection
    {
        try {
            $articles = $this->articleSearchService->search($term)->paginate($this->getPerPage($perPage));
            return ArticleResource::collection($articles);
        } catch (Throwable $e) {
            Log::error('Error in ArticleService: ' . $e->getMessage());
            throw new Exception('Error searching for articles', 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function filterArticles(array $filters, $perPage): AnonymousResourceCollection
    {
        try {
            $articles = $this->articleFilterService->filter($filters)->paginate($this->getPerPage($perPage));
            return ArticleResource::collection($articles);
        } catch (Throwable $e) {
            Log::error('Error in ArticleService: ' . $e->getMessage());
            throw new Exception('Error filtering for articles', 0, $e);
        }
    }

    /**
     * @throws Throwable
     * @throws AuthenticationException
     * @throws UserPreferenceNotFoundException
     */
    public function getArticlesByUserPreferences(int $perPage): AnonymousResourceCollection | JsonResponse
    {
        try {

            $articles = $this->userPreferenceService->getArticles()->paginate($this->getPerPage($perPage));

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
}
