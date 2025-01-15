<?php

namespace App\Services;

use App\Config\CacheTTLConfig;
use Illuminate\Support\Facades\Cache;
class CacheService
{

    public static function remember(string $key, int|null $expiration, $callback, $forget = false): mixed
    {
        if ($forget) {
            Cache::forget($key);
        }

        $expiration = CacheTTLConfig::getTtlInSeconds($expiration);

        return Cache::remember($key, $expiration, $callback);
    }

}
