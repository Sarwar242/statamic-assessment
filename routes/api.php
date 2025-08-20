<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;

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

// Blog Collection API
use App\Http\Controllers\Api\BlogController;
Route::get('/collections/blog', [BlogController::class, 'index']);

// Categories Taxonomy API
Route::get('/taxonomies/categories/terms', [BlogController::class, 'categories']);

// Cards Collection API
Route::get('/collections/cards', [App\Http\Controllers\Api\CardsController::class, 'index']);