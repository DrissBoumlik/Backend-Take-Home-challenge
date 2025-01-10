<?php

namespace App\Services;


use App\Exceptions\UserPreferenceNotFoundException;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserPreferenceService
{

    /**
     * @throws AuthenticationException
     * @throws UserPreferenceNotFoundException
     */
    public function getArticles(int $perPage)
    {
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
