<?php

return [
    'newsapi' => [
        'class' => \Domain\NewsApis\Services\Sources\NewsApiService::class,
        'config' => [
            'url' => 'https://newsapi.org/v2/top-headlines',
            'source' => 'NewsAPI',
            'apikey' => env('NEWSAPI_KEY'),
        ],
    ],
    'guardian' => [
        'class' => \Domain\NewsApis\Services\Sources\GuardianService::class,
        'config' => [
            'url' => 'https://content.guardianapis.com/search',
            'source' => 'The Guardian',
            'apikey' => env('GUARDIAN_API_KEY'),
        ],
    ],
    'ny_times' => [
        'class' => \Domain\NewsApis\Services\Sources\NYTimesService::class,
        'config' => [
            'url' => 'https://api.nytimes.com/svc/topstories/v2/home.json',
            'source' => 'New York Times',
            'apikey' => env('NYT_API_KEY'),
        ],
    ],
];
