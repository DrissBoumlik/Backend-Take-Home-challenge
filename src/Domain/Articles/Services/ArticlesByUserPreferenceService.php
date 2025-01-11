<?php

namespace Domain\Articles\Services;


use Domain\Articles\Exceptions\ArticlesByUserPreferenceNotFoundException;
use Domain\Articles\Models\Article;
use Domain\User\Models\UserPreference;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ArticlesByUserPreferenceService
{

    /**
     * @throws AuthenticationException
     * @throws ArticlesByUserPreferenceNotFoundException
     */
    public function getArticles(int $perPage): Builder
    {
        $user = Auth::user();

        if (! $user) {
            throw new AuthenticationException('Unauthenticated user');
        }

        $userPreferences = UserPreference::where('user_id', $user->id)->first();

        if (! $userPreferences) {
            throw new ArticlesByUserPreferenceNotFoundException('Articles by user preferences not found', Response::HTTP_NOT_FOUND);
        }

        $query = Article::query();

        $this->applyFilters($query, $userPreferences);

        return $query->latest('published_at');
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
