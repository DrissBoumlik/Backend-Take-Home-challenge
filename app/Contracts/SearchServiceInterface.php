<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface SearchServiceInterface
{
    public function search(string $term): Builder;
}
