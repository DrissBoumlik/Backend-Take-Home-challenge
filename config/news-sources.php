<?php

return [
    'newsapi' => [
        'class' => \App\Services\Sources\NewsApiSource::class,
        'config' => [
            'url' => 'https://newsapi.org/v2/top-headlines',
            'source' => 'NewsAPI',
            'apikey' => env('NEWSAPI_KEY'),
        ],
    ],
    'guardian' => [
        'class' => \App\Services\Sources\GuardianSource::class,
        'config' => [
            'url' => 'https://content.guardianapis.com/search',
            'source' => 'The Guardian',
            'apikey' => env('GUARDIAN_API_KEY'),
        ],
    ],
    'ny_times' => [
        'class' => \App\Services\Sources\NYTimesSource::class,
        'config' => [
            'url' => 'https://api.nytimes.com/svc/topstories/v2/home.json',
            'source' => 'New York Times',
            'apikey' => env('NYT_API_KEY'),
        ],
    ],
];
