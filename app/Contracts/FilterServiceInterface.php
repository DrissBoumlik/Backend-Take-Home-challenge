<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterServiceInterface
{
    public function filter(array $filters): Builder;
}
