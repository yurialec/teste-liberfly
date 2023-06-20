<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Group of routes to authentication
 *
 */
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
    });
});

/**
 *Group of routes to blog
 */
Route::group([
    'prefix' => 'blog'
], function () {
    Route::controller(BlogController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/create', 'create');
        Route::get('/{blog_id}', 'show');
        Route::post('/search', 'search');
        Route::put('/update/{blog_id}', 'update');
        Route::delete('/delete/{blog_id}', 'delete');
    });
});
