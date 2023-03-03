<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VizzyController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'client'], function() {

    Route::get('/latest-vizzys', [VizzyController::class, 'latestVizzys']);
    Route::get('/latest-vizzys-shows', [VizzyController::class, 'latestShowsWithVizzys']);
    Route::get('/top-shows', [VizzyController::class, 'topShows']);
    Route::get('/top-vizzy-shows', [VizzyController::class, 'topVizzyShows']);
    Route::get('/vizzy', [VizzyController::class, 'show']);
    Route::get('/vizzy-categories', [VizzyController::class, 'vizzyCategories']);
    Route::get('/has-vizzy', [VizzyController::class, 'hasVizzy']);
    Route::get('/podcast-vizzy-guids', [VizzyController::class, 'getAllVizzyGuidByPodcast']);

});