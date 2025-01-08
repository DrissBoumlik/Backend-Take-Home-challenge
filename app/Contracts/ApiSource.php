<?php

namespace App\Contracts;

interface ApiSource
{
    public function fetchArticles(): self;

    public function parse(): array;
}
