<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PodcastCategoryController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\FavouritedController;
use App\Http\Controllers\Api\PlayedController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\ShareController;
use App\Http\Controllers\Api\VizzyController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\LogController;
use App\Http\Resources\User as UserResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/social-login', [AuthController::class, 'socialLogin']);
Route::post('/forgot', [AuthController::class, 'forgot']);
Route::post('/forgot-confirm', [AuthController::class, 'forgotConfirm']);
Route::post('/forgot-reset', [AuthController::class, 'forgotReset']);

Route::group(['middleware' => 'auth:api'], function() {

    // User related API calls
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/user-details', [AuthController::class, 'editDetails']);
    Route::post('/user-categories', [PodcastCategoryController::class, 'updateUserPodcastCategory']);
    Route::post('/user-favourited', [FavouritedController::class, 'addFavourite']);
    Route::post('/user-favourited-delete', [FavouritedController::class, 'removeFavourite']);
    Route::post('/user-played', [PlayedController::class, 'addPlayed']);
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    // Podcast Browsing related API Calls
    Route::get('/index', [IndexController::class, 'index']);
    Route::get('/vizzy-index', [IndexController::class, 'vizzyIndex']);
    Route::get('/latest-vizzys', [IndexController::class, 'allLatestVizzys']);
    Route::get('/top-shows', [IndexController::class, 'allTopShows']);
    Route::get('/favourited', [IndexController::class, 'allFavourited']);
    Route::get('/favourited/shows', [IndexController::class, 'allFavouritedShows']);
    Route::get('/favourited/episodes', [IndexController::class, 'allFavouritedEpisodes']);
    Route::get('/recently-played', [IndexController::class, 'allRecentlyPlayedShows']);
    Route::get('/categories', [IndexController::class, 'allCategories']);
    Route::get('/latest', [IndexController::class, 'allLatestShows']);
    Route::get('/trending', [IndexController::class, 'allTrendingShows']);
    Route::get('/show', [PodcastController::class, 'show']);


    // Player related API calls
    Route::get('/episode', [PlayerController::class, 'episode']);
    Route::post('/share', [ShareController::class, 'share']);
    Route::post('/snippet', [ShareController::class, 'saveSnippet']);
    Route::get('/snippet', [ShareController::class, 'loadSnippet']);
    Route::post('/snippet/{snippet}', [ShareController::class, 'updateSnippet']);
    Route::delete('/snippet', [ShareController::class, 'deleteSnippet']);

    // Search
    Route::post('/search', [SearchController::class, 'search']);
    Route::get('/popular-search-terms', [SearchController::class, 'getPopularSearchTerms']);

    // Log
    Route::post('/activity-log', [LogController::class, 'activity']);
});

Route::group(['middleware' => 'client'], function() {

    // Allow Category management through Podcaster Admin portal
    Route::get('/podcast-categories', [PodcastCategoryController::class, 'podcastCategories']);
    Route::post('/podcast-category', [PodcastCategoryController::class, 'storePodcastCategory']);
    Route::get('/podcast-category/{podcastCategory}', [PodcastCategoryController::class, 'editPodcastCategory']);
    Route::post('/podcast-category/{podcastCategory}', [PodcastCategoryController::class, 'updatePodcastCategory']);
    Route::delete('/podcast-category/{podcastCategory}', [PodcastCategoryController::class, 'deletePodcastCategory']);

    // update saved has_vizzy flag on recently play and favourited episode
    // when vizzy is published/unpublished
    Route::post('/vizzy/published', [VizzyController::class, 'publish']);
    Route::post('/vizzy/unpublished', [VizzyController::class, 'unpublish']);

    Route::post('/share-slug', [ShareController::class, 'shareSlug']);

});

Route::fallback(function(){
    return response()->json([
        'success' => false,
        'message' => 'Page Not Found.'], 404);
});