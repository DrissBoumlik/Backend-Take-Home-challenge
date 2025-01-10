<?php

namespace App\Services;


class DataSourceManager
{
    public function getDataSources(): array
    {
        $config = config('news-sources');
        return array_map(
            fn($source): mixed => $source['class'],
            $config
        );
    }
}
