<?php

namespace App\Services;

use App\Contracts\FilterServiceInterface;
use App\Models\Article;

class ArticleFilterService implements FilterServiceInterface
{

    public function filter(array $filters): \Illuminate\Database\Eloquent\Builder
    {
        $query = Article::query();

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['source'])) {
            $query->where('source', 'like', $filters['source']);
        }

        if (! empty($filters['start_date'])) {
            $query->whereDate('published_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('published_at', '<=', $filters['end_date']);
        }

        return $query->select([
            'title',
            'content',
            'author',
            'source',
            'category',
            'published_at'
        ])->latest('published_at');
    }
}
