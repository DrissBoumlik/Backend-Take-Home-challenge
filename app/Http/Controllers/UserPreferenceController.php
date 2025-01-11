<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use App\Services\User\UserPreferenceService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
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
        } catch (AuthenticationException $e) {
            Log::warning('Unauthenticated user: ' . $e->getMessage());

            return response()->json(['message' => 'Unauthenticated user', ], Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $e) {
            Log::error('Error in getting user preferences: ' . $e->getMessage(), [ 'exception' => $e ]);
            return response()->json([
                'message' => 'Failed to retrieve user preferences',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
