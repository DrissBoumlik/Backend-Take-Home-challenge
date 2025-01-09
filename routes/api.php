<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/articles', [ArticleController::class, 'index']);

Route::get('/articles/search', [ArticleController::class, 'search']);

Route::get('/articles/filter', [ArticleController::class, 'filter']);

Route::get('/user/preferences', [UserPreferenceController::class, 'index']);
