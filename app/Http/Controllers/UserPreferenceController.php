<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use App\Services\User\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class UserPreferenceController extends Controller
{

    public function __construct(public readonly UserPreferenceService $userPreferenceService)
    {

    }
    public function index(Request $request): AnonymousResourceCollection | JsonResponse
    {
        try {
            return CacheService::remember('user-preferences', config('cache-properties.ttl.default'), function () {

                return $this->userPreferenceService->getPreferences();

            }, $request->has('forget'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user preferences',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
