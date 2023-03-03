<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Admin\UserController;
use \App\Http\Controllers\Admin\PodcastCategoryController;
use App\Http\Controllers\Admin\ExportController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

Route::middleware('verified')->group(function () {
    Route::get('/', 'App\Http\Controllers\StudioController@index')->name('studio');
    Route::get('/profile', 'App\Http\Controllers\PageController@profile')->name('profile');
    Route::post('/profile', 'App\Http\Controllers\PageController@profile')->name('profile');
    Route::get('/help', 'App\Http\Controllers\PageController@help')->name('help');

    Route::get('/podcasts', 'App\Http\Controllers\PodcastController@podcasts')->name('podcasts');
    Route::get('/add-podcast', 'App\Http\Controllers\PodcastController@addPodcast')->name('add-podcast');
    Route::post('/confirm-details', 'App\Http\Controllers\PodcastController@confirmDetails')->name('confirm-details');
    Route::post('/search-podcast', 'App\Http\Controllers\PodcastController@searchPodcast')->name('search-podcast');
    Route::post('/check-podcast-owner', 'App\Http\Controllers\PodcastController@checkPodcastOwner')->name('check-podcast-owner');
    Route::post('/send-podcast-verification', 'App\Http\Controllers\PodcastController@sendPodcastverification')->name('send-podcast-verification');
    Route::post('/submit-podcast-verification', 'App\Http\Controllers\PodcastController@submitPodcastverification')->name('submit-podcast-verification');
    Route::post('/refresh-podcast', 'App\Http\Controllers\PodcastController@refreshPodcast')->name('refresh-podcast');

    Route::get('/vizzies', 'App\Http\Controllers\PodcastController@vizzies')->name('vizzies');
    Route::get('/podcasts/{podcast}/episodes', 'App\Http\Controllers\PodcastController@episodes')->name('episodes');
    Route::get('/podcasts/{podcast}/curator', 'App\Http\Controllers\CuratorController@show')->name('curator');
    Route::post('/podcasts/{podcast}/curator/save', 'App\Http\Controllers\CuratorController@store')->name('curator-save');
    Route::post('/podcasts/{podcast}/curator/autosave', 'App\Http\Controllers\CuratorController@autoSave')->name('curator-autosave');
    Route::post('/podcasts/{podcast}/curator/publish', 'App\Http\Controllers\CuratorController@publish')->name('curator-publish');
    Route::post('/podcasts/{podcast}/curator/unpublish', 'App\Http\Controllers\CuratorController@unpublish')->name('curator-unpublish');
    Route::delete('/vizzy/{vizzy}', 'App\Http\Controllers\CuratorController@delete')->name('vizzy-delete');

    // chapter tool
    Route::get('/studio', 'App\Http\Controllers\StudioController@index')->name('studio');
    Route::post('/studio/upload', 'App\Http\Controllers\StudioController@upload')->name('studio-upload');
    Route::get('/studio/chapter-editor/{audio}', 'App\Http\Controllers\StudioController@tool')->name('studio-tool');
    Route::post('/studio/chapter-editor/{audio}/save', 'App\Http\Controllers\StudioController@save')->name('studio-save');
    Route::post('/studio/chapter-editor/{audio}/upload-image', 'App\Http\Controllers\StudioController@imageUpload')->name('studio-image-upload');
    Route::post('/studio/chapter-editor/{audio}/delete-image', 'App\Http\Controllers\StudioController@imageDelete')->name('studio-image-delete');
    Route::post('/studio/chapter-editor/{audio}/download', 'App\Http\Controllers\StudioController@download')->name('studio-download');
    Route::post('/studio/chapter-editor/{audio}/show-notes', 'App\Http\Controllers\StudioController@showNotes')->name('studio-show-notes');
    Route::post('/studio/chapter-editor/{audio}/delete', 'App\Http\Controllers\StudioController@delete')->name('studio-delete');

    Route::impersonate();
});

Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\Admin\DashboardController@dashboard')->name('dashboard');
    Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);

    // manage show category
    Route::get('/podcast-categories/report', [PodcastCategoryController::class,'report'])->name('podcast-categories.report');;
    Route::resource('podcast-categories', PodcastCategoryController::class);

    // manage top shows
    Route::get('/top-shows', 'App\Http\Controllers\Admin\TopShowController@index')->name('top-shows.index');
    Route::post('/top-shows/add', 'App\Http\Controllers\Admin\TopShowController@add')->name('top-shows.add');
    Route::post('/top-shows/delete', 'App\Http\Controllers\Admin\TopShowController@delete')->name('top-shows.delete');
    Route::post('/top-shows/order', 'App\Http\Controllers\Admin\TopShowController@order')->name('top-shows.order');

    // manage podcasts
    Route::get('/podcasts', 'App\Http\Controllers\Admin\PodcastController@index')->name('podcasts.index');
    Route::post('/podcasts/add', 'App\Http\Controllers\Admin\PodcastController@add')->name('podcasts.add');

    // manage vizzys
    Route::get('/vizzys', 'App\Http\Controllers\Admin\VizzyController@index')->name('vizzys.index');
    Route::post('/vizzys/{vizzy}/approve', 'App\Http\Controllers\Admin\VizzyController@approve')->name('vizzys.approve');
    Route::post('/vizzys/{vizzy}/reject', 'App\Http\Controllers\Admin\VizzyController@reject')->name('vizzys.reject');

    // export users
    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export-app-users', [ExportController::class, 'exportAppUsers'])->name('export.appusers');
    Route::get('/export-activity-log', [ExportController::class, 'exportAppActivityLogs'])->name('export.appactivitylogs');
    Route::get('/export-played', [ExportController::class, 'exportAppPlayed'])->name('export.appplayed');
});

Route::middleware('auth')->group(function () {
    Route::get('/guidelines', 'App\Http\Controllers\PageController@guidelines')->name('guidelines');
    Route::post('/guidelines', 'App\Http\Controllers\PageController@guidelines')->name('guidelines');
    Route::get('/terms', 'App\Http\Controllers\PageController@terms')->name('terms');
});

Route::get('/{slug}', 'App\Http\Controllers\ShareController@share');

// MediaManager
ctf0\MediaManager\MediaRoutes::routes();