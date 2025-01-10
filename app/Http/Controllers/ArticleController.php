<?php

namespace App\Http\Controllers;

use App\Exceptions\UserPreferenceNotFoundException;
use App\Http\Requests\ArticleFilterRequest;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\ArticleSearchRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articleService)
    {
    }

    public function index(ArticleRequest $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page');

        $articles = $this->articleService->getAllArticles($perPage);

        return ArticleResource::collection($articles);
    }


    /**
     * @throws \Exception
     */
    public function search(ArticleSearchRequest $request): AnonymousResourceCollection
    {
        $term = $request->input('term');

        $perPage = (int) $request->get('per_page');

        $filteredArticles = $this->articleService->searchArticles($term, $perPage);

        return ArticleResource::collection($filteredArticles);
    }

    public function filter(ArticleFilterRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $filters = $request->only(['category', 'source', 'start_date', 'end_date']);

        $perPage = (int) $request->get('per_page');

        $filteredArticles = $this->articleService->filterArticles($filters, $perPage);

        return ArticleResource::collection($filteredArticles);
    }

    public function getArticlesByPreferences(ArticleRequest $request): AnonymousResourceCollection|JsonResponse
    {
        try {

            $perPage = (int)$request->get('per_page');

            return $this->articleService->getArticlesByPreferences($perPage);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.' ],
                Response::HTTP_BAD_REQUEST);
        }
    }
}
