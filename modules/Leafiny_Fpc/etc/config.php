<?php

$config = [
    'model' => [
        'admin_cache' => [
            'cache' => [
                'fpc' => [
                    'name'        => 'FPC',
                    'description' => 'Full Page Cache',
                    'flush'       => '/admin/*/cache/flush/fpc/',
                    'active'      => 'page.default.fpc'
                ],
            ],
        ],
    ],

    'page' => [
        '/admin/*/cache/flush/fpc/' => [
            'class'    => Fpc_Page_Cache_Flush::class,
            'template' => null,
        ]
    ],

    'helper' => [
        'fpc_cache' => [
            'class' => Fpc_Helper_Cache::class,
            'no_cache_identifiers' => ['http_404'],
            'allowed_params' => ['p'],
        ]
    ],

    'observer' => [
        'page_render_before' => [
            0 => 'fpc_get_cache',
        ],
        'page_render_after' => [
            0 => 'fpc_save_cache',
            1 => 'fpc_clean_html',
        ],
        'object_save_before' => [
            0 => 'fpc_flush_cache',
        ]
    ],

    'event' => [
        'fpc_get_cache' => [
            'class' => Fpc_Observer_GetCache::class,
        ],
        'fpc_save_cache' => [
            'class' => Fpc_Observer_SaveCache::class,
        ],
        'fpc_flush_cache' => [
            'class' => Fpc_Observer_FlushCache::class,
        ],
        'fpc_clean_html' => [
            'class' => Fpc_Observer_CleanHtml::class
        ]
    ],
];
