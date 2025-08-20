<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Statamic\Facades\Entry;

class CardsController extends BaseController
{
    public function index(Request $request)
    {
        $query = Entry::query()->where('collection', 'cards');

        if ($request->has('filter.title:contains')) {
            $title = $request->input('filter.title:contains');
            $query->where('title', 'like', "%{$title}%");
        }

        $entries = $query->get();

        $data = $entries->map(function ($entry) {
            return [
                'id' => $entry->id(),
                'title' => $entry->get('title'),
                'subtitle' => $entry->get('subtitle'),
                'slug' => $entry->slug(),
                'excerpt' => $entry->get('excerpt'),
                'featured_image' => $entry->get('featured_image'),
                'priority' => $entry->get('priority'),
                'status' => $entry->get('status', 'published'),
            ];
        });

        return response()->json([
            'data' => $data
        ]);
    }
}