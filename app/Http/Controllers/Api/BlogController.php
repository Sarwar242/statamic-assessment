<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;

class BlogController extends BaseController
{
	public function index(Request $request)
	{
		// Start with a query builder to ensure we can call get() correctly
		$query = Entry::query()->where('collection', 'blog');

		$entries = $query->get();

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

		$total = $entries->count();

		// Pagination
		$page = (int) $request->input('page', 1);
		$perPage = 6;
		$offset = ($page - 1) * $perPage;
		$entries = $entries->slice($offset, $perPage)->values();

		$data = $entries->map(function ($entry) {
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
				'published_at' => optional($entry->date())->format('Y-m-d'),
				'categories' => $categories,
				'featured' => $entry->get('featured', false),
			];
		});

		return response()->json([
			'data' => $data,
			'meta' => [
				'current_page' => $page,
				'last_page' => (int) ceil($total / $perPage),
				'per_page' => $perPage,
				'total' => $total,
			],
		]);
	}

	public function categories(Request $request)
	{
		$terms = Term::query()->where('taxonomy', 'categories')->get();

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
			'data' => $data,
		]);
	}
} 