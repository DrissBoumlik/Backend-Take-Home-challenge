<?php

namespace App\Http\Controllers;

use App\Exceptions\UserPreferenceNotFoundException;
use App\Http\Requests\ArticleFilterRequest;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\ArticleSearchRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articleService)
    {
    }

    public function index(ArticleRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page');

        $articles = $this->articleService->getAllArticles($perPage);

        return ArticleResource::collection($articles);
    }


    /**
     * @throws \Exception
     */
    public function search(ArticleSearchRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
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

    public function getArticlesByPreferences(ArticleRequest $request)
    {
        try {

            $perPage = (int) $request->get('per_page');

            return $this->articleService->getArticlesByPreferences($perPage);

        } catch (UserPreferenceNotFoundException $e) {
            Log::warning('User preferences not found: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return response()->json(['message' => 'User preferences not found' ], Response::HTTP_NOT_FOUND);
        } catch (AuthenticationException $e) {
            Log::warning('Unauthenticated user: ' . $e->getMessage(), [ 'user_id' => auth()->id() ]);
            return response()->json(['error' => 'Unauthenticated user', ], Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $e) {
            Log::error('Error in getting articles by preferences: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json(['error' => 'An unexpected error occurred. Please try again later.' ],
                Response::HTTP_BAD_REQUEST);
        }
    }
}
