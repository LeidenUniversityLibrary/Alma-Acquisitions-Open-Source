<?php

use App\Http\Controllers\AcquisitionsListsController;
use App\Http\Controllers\ForceRefreshAllAcquisitionsListsController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ImportXMLController;
use App\Http\Controllers\RefreshSingleAcquisitionsListController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SitemapXmlController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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

//SECTION   Homepage
Route::get('/', [HomepageController::class, 'index'])->name('landing_page');

//SECTION   Admin
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', [AcquisitionsListsController::class, 'index'])->name('home');

    Route::get('/create/{acquisitionListTitle}', [AcquisitionsListsController::class, 'create'])->name('create_new_acquisitions_list');
    Route::post('/create/{acquisitionListTitle}', [AcquisitionsListsController::class, 'store']);

    Route::get('/edit/{acquisitionListTitle}', [AcquisitionsListsController::class, 'edit'])->name('update_acquisitions_list');
    Route::post('/edit/{acquisitionListTitle}', [AcquisitionsListsController::class, 'update']);

    Route::get('/import/{acquisitionListTitle}', ImportXMLController::class)->name('import_xml_file');
    Route::get('/refresh/{acquisitionListTitle}', RefreshSingleAcquisitionsListController::class)->name('refresh_single_acquisitions_list');
    Route::get('/force_refresh', ForceRefreshAllAcquisitionsListsController::class)->name('force_refresh_database');

    Route::delete('/{acquisitionListTitle}', [AcquisitionsListsController::class, 'destroy'])->name('delete_acquisitions_list');
});

//SECTION   Disable routes for generating additional users.
Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

//SECTION   Sitemap
Route::get('/sitemap.xml', [SitemapXmlController::class, 'index']);

//SECTION   RSS feed
Route::get('/feed/{feed_url_path}', RssFeedController::class)->name('feed_acquisitions_list');

//SECTION   Application endpoint
Route::get('/{acquisitionListTitle}', [AcquisitionsListsController::class, 'show'])->name('read_acquisitions_list');





