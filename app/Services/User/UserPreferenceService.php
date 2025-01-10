<?php

namespace App\Services\User;


use App\Http\Resources\UserPreferenceResource;
use App\Models\UserPreference;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserPreferenceService
{

    /**
     * @throws Exception
     */
    public function getPreferences(): AnonymousResourceCollection
    {
        try {
            $user = Auth::user();

            $preferences = UserPreference::where('user_id', $user->id)->get();

            return UserPreferenceResource::collection($preferences);
        } catch (Throwable $e) {
            Log::error('Error in UserPreferenceService: ' . $e->getMessage());
            throw new Exception('Failed retrieving user preferences: ' . $e->getMessage(), 0, $e);
        }

    }
}
