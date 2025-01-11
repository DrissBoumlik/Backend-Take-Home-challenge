<?php

namespace Domain\NewsApis\Contracts;

interface ApiSource
{
    public function fetchArticles(): self;

    public function parse(): array;
}
