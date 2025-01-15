<?php

namespace App\Config;

class CacheTTLConfig
{
    public const DEFAULT_TTL_IN_SECONDS = 3600;

    public static function getTtlInSeconds(?int $ttl = null): mixed
    {
        return $ttl ?? static::DEFAULT_TTL_IN_SECONDS;
    }
}
