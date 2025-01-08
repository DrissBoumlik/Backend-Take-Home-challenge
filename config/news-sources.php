<?php

return [
    'newsapi' => [
//        'class' => \App\Services\Sources\NewsApiSource::class,
        'config' => [
            'url' => 'https://newsapi.org/v2/top-headlines',
            'source' => 'NewsAPI',
            'apikey' => env('NEWSAPI_KEY'),
        ],
    ],
];
