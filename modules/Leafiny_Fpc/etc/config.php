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
        /* Cache Management */
        '/admin/*/cache/list/' => [
            'children' => [
                'fpc_key' => 'admin.fpc.cache.flush.key',
            ],
        ],
        '/admin/*/cache/flush/fpc/' => [
            'class'    => Fpc_Page_Cache_Flush::class,
            'template' => null,
        ],
        '/admin/*/cache/flush/fpc/key/' => [
            'class'    => Fpc_Page_Cache_Flush_Key::class,
            'template' => null,
        ]
    ],

    'block' => [
        'admin.fpc.cache.flush.key' => [
            'template' => 'Leafiny_Fpc::block/cache/key.twig',
            'class'    => Fpc_Block_Cache_Key::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
    ],

    'helper' => [
        'fpc_cache' => [
            'class' => Fpc_Helper_Cache::class,
            'no_cache_identifiers' => ['http_404'],
            'allowed_params' => [],
        ]
    ],

    'observer' => [
        'page_render_before' => [
            'fpc_get_cache' => 10,
        ],
        'page_render_after' => [
            'fpc_save_cache' => 10,
            'fpc_clean_html' => 20,
        ],
        'object_save_before' => [
            'fpc_flush_cache' => 10,
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
