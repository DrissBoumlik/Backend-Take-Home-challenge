<?php

namespace App\Services;

use App\Config\CacheTTLConfig;
use Illuminate\Support\Facades\Cache;
class CacheService
{
    public const CACHE_EXPIRATION = CacheTTLConfig::DEFAULT_TTL_IN_SECONDS;

    public function __construct()
    {

    }

    public static function remember(string $key, int|null $expiration, $callback, $forget = false)
    {
        if ($forget) {
            Cache::forget($key);
        }

        $expiration = $expiration ?: self::CACHE_EXPIRATION;

        return Cache::remember($key, $expiration, $callback);
    }

}
