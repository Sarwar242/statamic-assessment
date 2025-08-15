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
Route::get('/collections/blog', function (Request $request) {
    $query = Entry::query()->collection('blog')->published();
    
    // Filter by category
    if ($request->has('filter.categories:contains')) {
        $category = $request->input('filter.categories:contains');
        $query->where('categories', 'like', "%$category%");
    }
    
    // Filter by title
    if ($request->has('filter.title:contains')) {
        $title = $request->input('filter.title:contains');
        $query->where('title', 'like', "%$title%");
    }
    
    // Pagination
    $page = $request->input('page', 1);
    $perPage = 6;
    $offset = ($page - 1) * $perPage;
    
    $total = $query->count();
    $entries = $query->offset($offset)->limit($perPage)->get();
    
    $data = $entries->map(function ($entry) {
        // Ensure categories is always an array
        $categories = $entry->get('categories', []);
        if (is_string($categories)) {
            $categories = [$categories];
        } elseif (!is_array($categories)) {
            $categories = [];
        }
        
        return [
            'id' => $entry->id(),
            'title' => $entry->get('title'),
            'slug' => $entry->slug(),
            'excerpt' => $entry->get('excerpt'),
            'content' => $entry->get('content'),
            'featured_image' => $entry->get('featured_image'),
            'author_name' => $entry->get('author_name'),
            'published_at' => $entry->date()->format('Y-m-d'),
            'categories' => $categories,
            'featured' => $entry->get('featured', false),
        ];
    });
    
    return response()->json([
        'data' => $data,
        'meta' => [
            'current_page' => (int) $page,
            'last_page' => ceil($total / $perPage),
            'per_page' => $perPage,
            'total' => $total,
        ]
    ]);
});

// Categories Taxonomy API
Route::get('/taxonomies/categories/terms', function (Request $request) {
    $terms = Term::query()->taxonomy('categories')->get();
    
    $data = $terms->map(function ($term) {
        return [
            'id' => $term->id(),
            'title' => $term->get('title'),
            'slug' => $term->slug(),
            'description' => $term->get('description'),
            'color' => $term->get('color'),
        ];
    });
    
    return response()->json([
        'data' => $data
    ]);
}); 