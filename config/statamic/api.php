<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the Statamic API settings.
    |
    */

    'enabled' => true,

    'resources' => [
        'collections' => [
            'blog' => [
                'allowed_filters' => ['categories', 'title', 'featured'],
                'allowed_sorts' => ['title', 'published_at', 'featured'],
                'allowed_fields' => ['title', 'slug', 'excerpt', 'content', 'featured_image', 'author_name', 'published_at', 'categories', 'featured'],
            ],
        ],
        'taxonomies' => [
            'categories' => [
                'allowed_filters' => ['title'],
                'allowed_sorts' => ['title'],
                'allowed_fields' => ['title', 'slug', 'description', 'color'],
            ],
        ],
    ],

    'pagination' => [
        'per_page' => 10,
        'max_per_page' => 100,
    ],

    'caching' => [
        'enabled' => false,
        'ttl' => 3600,
    ],
];
