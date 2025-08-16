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
    // In Statamic 5, we need to use the correct query syntax
    // Let's try using the repository pattern instead
    $entries = Entry::query()->collection('blog')->get();
    
    // Filter by category
    if ($request->has('filter.categories:contains')) {
        $category = $request->input('filter.categories:contains');
        $entries = $entries->filter(function ($entry) use ($category) {
            $categories = $entry->get('categories', []);
            if (is_string($categories)) {
                $categories = [$categories];
            }
            return in_array($category, $categories);
        });
    }
    
    // Filter by title
    if ($request->has('filter.title:contains')) {
        $title = $request->input('filter.title:contains');
        $entries = $entries->filter(function ($entry) use ($title) {
            return str_contains(strtolower($entry->get('title', '')), strtolower($title));
        });
    }
    
    // Get total count after filtering
    $total = $entries->count();
    
    // Pagination
    $page = $request->input('page', 1);
    $perPage = 6;
    $offset = ($page - 1) * $perPage;
    
    // Apply pagination - ensure we get a countable collection
    $entries = $entries->slice($offset, $perPage)->values();
    
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