<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleFilterRequest;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\ArticleSearchRequest;
use App\Services\Article\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articleService)
    {
    }

    public function index(ArticleRequest $request): AnonymousResourceCollection | JsonResponse
    {
        try {
            $perPage = (int)$request->get('per_page');

            return $this->articleService->getAllArticles($perPage);
        } catch (Throwable $e) {
                return response()->json([
                    'message' => 'Failed to retrieve articles',
                ], Response::HTTP_BAD_REQUEST);
            }
    }


    public function search(ArticleSearchRequest $request): AnonymousResourceCollection | JsonResponse
    {
        try {
            $term = $request->input('term');

            $perPage = (int) $request->get('per_page');

            return $this->articleService->searchArticles($term, $perPage);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to search articles',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function filter(ArticleFilterRequest $request): AnonymousResourceCollection | JsonResponse
    {
        try {
            $filters = $request->only(['category', 'source', 'start_date', 'end_date']);

            $perPage = (int) $request->get('per_page');

            return $this->articleService->filterArticles($filters, $perPage);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to filter articles',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getArticlesByPreferences(ArticleRequest $request): AnonymousResourceCollection | JsonResponse
    {
        try {

            $perPage = (int)$request->get('per_page');

            return $this->articleService->getArticlesByPreferences($perPage);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'An unexpected error occurred. Please try again later.' ],
                Response::HTTP_BAD_REQUEST);
        }
    }
}
