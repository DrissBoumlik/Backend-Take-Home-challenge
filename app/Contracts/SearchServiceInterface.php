<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SearchServiceInterface
{
    public function search(string $term);
}
