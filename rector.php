<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
//        __DIR__ . '/app/Config',
//        __DIR__ . '/app/Console/Commands',
//        __DIR__ . '/app/Contracts',
//        __DIR__ . '/app/Http',
//        __DIR__ . '/app/Models',
//        __DIR__ . '/app/Services',
        __DIR__ . '/app',
//        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
//        __DIR__ . '/public',
//        __DIR__ . '/resources',
//        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withTypeCoverageLevel(10)
    ->withDeadCodeLevel(10)
    ->withCodeQualityLevel(10)
    ;
