<?php

namespace Domain\Articles\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Domain\Articles\Http\Requests\ArticleFilterRequest;
use Domain\Articles\Http\Requests\ArticleRequest;
use Domain\Articles\Http\Requests\ArticleSearchRequest;
use Domain\Articles\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articleService)
    {
    }

    public function index(ArticleRequest $request): mixed
    {
        try {
            return CacheService::remember('articles.index', config('cache-properties.ttl.default'), function () use ($request) {

                $perPage = (int) $request->get('per_page');

                return $this->articleService->getAllArticles($perPage);

            }, $request->has('forget'));
        } catch (Throwable $e) {
                return response()->json([
                    'message' => 'Failed to retrieve articles',
                ], Response::HTTP_BAD_REQUEST);
            }
    }


    public function search(ArticleSearchRequest $request): mixed
    {
        try {
            return CacheService::remember('articles.search', config('cache-properties.ttl.default'), function () use ($request) {
                $term = $request->input('term');

                $perPage = (int) $request->get('per_page');

                return $this->articleService->searchArticles($term, $perPage);
            }, $request->has('forget'));
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to search articles',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function filter(ArticleFilterRequest $request): mixed
    {
        try {
            return CacheService::remember('articles.filter', config('cache-properties.ttl.default'), function () use ($request) {
                $filters = $request->only(['category', 'source', 'start_date', 'end_date']);

                $perPage = (int) $request->get('per_page');

                return $this->articleService->filterArticles($filters, $perPage);
            }, $request->has('forget'));
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to filter articles',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getArticlesByPreferences(ArticleRequest $request): mixed
    {
        try {
            return CacheService::remember('articles.user-preferences', config('cache-properties.ttl.default'), function () use ($request) {

                $perPage = (int) $request->get('per_page');

                return $this->articleService->getArticlesByUserPreferences($perPage);
            }, $request->has('forget'));
        } catch (Throwable $e) {
            return response()->json(['message' => 'An unexpected error occurred. Please try again later.' ],
                Response::HTTP_BAD_REQUEST);
        }
    }
}
