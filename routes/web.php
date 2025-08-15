<?php

use Illuminate\Support\Facades\Route;

// Statamic routes for our components
Route::statamic('/', 'home');
Route::statamic('blog', 'blog');
Route::statamic('cards', 'cards');

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);
