<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserPreferenceResource;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserPreferenceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            $preferences = UserPreference::where('user_id', $user->id)->get();

            return response()->json(UserPreferenceResource::collection($preferences));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user preferences',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
