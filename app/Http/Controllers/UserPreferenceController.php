<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserPreferenceResource;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public function index(Request $request)
    {
         $user = Auth::user();
         $preferences = UserPreference::where('user_id', $user->id)->get();

        // $preferences = UserPreference::inRandomOrder()->get(); // testing purposes

        return response()->json(UserPreferenceResource::collection($preferences));
    }
}
