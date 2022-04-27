<?php

$config = [
    'model' => [
        'search_fulltext' => [
            'class' => Search_Model_Search_Fulltext::class,
        ],
        'admin_cache' => [
            'cache' => [
                'search' => [
                    'name'        => 'Data Search',
                    'description' => 'Index search data',
                    'flush'       => '/admin/*/search/refresh/',
                    'active'      => true,
                ],
            ],
        ],
    ],

    'helper' => [
        'search' => [
            'class'         => Search_Helper_Search::class,
            'engine'        => 'search_fulltext',
            'nearest_words' => true,
        ]
    ],

    'block' => [
        'search' => [
            'class'    => Search_Block_Form::class,
            'template' => 'Leafiny_Search::block/form.twig',
        ]
    ],

    'page' => [
        '/search/' => [
            'class'        => Search_Page_Result::class,
            'content'      => 'Leafiny_Search::page/result.twig',
            'meta_title'   => 'Search Results',
            'robots'       => 'NOINDEX,NOFOLLOW',
            'allow_params' => 1,
            'fpc'          => 0,
        ],

        '/admin/*/search/refresh/' => [
            'class'    => Search_Page_Backend_Refresh::class,
            'template' => null,
        ],
    ],

    'events' => [
        'object_save_after' => [
            'refresh_object' => 100,
        ],
        'object_delete_after' => [
            'remove_object' => 100,
        ],
    ],

    'observer' => [
        'refresh_object' => [
            'class' => Search_Observer_Refresh::class,
        ],
        'remove_object' => [
            'class' => Search_Observer_Remove::class,
        ],
    ],
];

