<?php

namespace App\Exceptions;

class UserPreferenceNotFoundException extends \Exception
{
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 404);
    }
}
