<?php

namespace Domain\NewsApis\Contracts;

interface ApiSource
{
    public function setConfig(): self;

    public function fetchArticles(): self;

    public function parse(): array;
}
