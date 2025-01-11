<?php

namespace Domain\Articles\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ArticlesByUserPreferenceNotFoundException extends \Exception
{
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], Response::HTTP_NOT_FOUND);
    }
}
