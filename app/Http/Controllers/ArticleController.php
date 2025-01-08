<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Http\Request;

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
}
