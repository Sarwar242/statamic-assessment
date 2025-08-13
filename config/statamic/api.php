<?php

return [
    'enabled' => env('STATAMIC_API_ENABLED', false),

    'default_auth_guard' => env('STATAMIC_API_DEFAULT_GUARD', 'web'),

    'resources' => [
        'collections' => [
            'blog' => [
                'allowed_filters' => [
                    'title',
                    'categories',
                    'published_at',
                    'author_name'
                ],
                'fields' => [
                    'id',
                    'title',
                    'slug',
                    'featured_image',
                    'excerpt',
                    'categories',
                    'author_name',
                    'author_title',
                    'author_avatar',
                    'published_at',
                    'api_url'
                ]
            ]
        ],

        'taxonomies' => [
            'categories' => [
                'fields' => [
                    'id',
                    'title',
                    'slug',
                    'description',
                    'color'
                ]
            ]
        ]
    ],

    'cache' => false,

    'pagination' => [
        'max_items' => 25,
        'default_limit' => 10,
    ]
];