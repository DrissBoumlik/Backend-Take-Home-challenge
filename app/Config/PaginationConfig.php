<?php

namespace App\Config;

class PaginationConfig
{
    public const DEFAULT_PER_PAGE = 15;
    public const MAX_PER_PAGE = 100;

    public static function getPerPage(?int $perPage = null): mixed
    {
        $perPage = $perPage ?: static::DEFAULT_PER_PAGE;
        return min($perPage, static::MAX_PER_PAGE);
    }
}
