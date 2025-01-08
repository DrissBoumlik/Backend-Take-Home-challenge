<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface FilterServiceInterface
{
    public function filter(array $filters);
}
