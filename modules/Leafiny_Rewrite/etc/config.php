<?php

$config = [
    'model' => [
        'rewrite' => [
            'class' => Rewrite_Model_Rewrite::class
        ],
    ],

    'page' => [
        '/admin/*/rewrite/list/' => [
            'title'            => 'Rewrites',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Rewrite::page/backend/rewrite/list.twig',
            'model_identifier' => 'rewrite',
            'meta_title'       => 'Rewrites',
            'meta_description' => '',
            'list_buttons'     => [
                '/admin/*/rewrite/refresh/' => 'Refresh',
                '/admin/*/rewrite/new/'     => 'Add',
            ]
        ],
        '/admin/*/rewrite/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'rewrite',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/rewrite/refresh/' => [
            'class'              => Rewrite_Page_Backend_Refresh::class,
            'model_identifier'   => 'rewrite',
            'referer_identifier' => '/admin/*/rewrite/list/',
            'template'           => null,
            'allow_params'       => 1,
        ],
        '/admin/*/rewrite/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/rewrite/edit/' => [
            'title'              => 'Edit',
            'template'           => 'Leafiny_Backend::page.twig',
            'class'              => Backend_Page_Admin_Form::class,
            'content'            => 'Leafiny_Rewrite::page/backend/rewrite/form.twig',
            'referer_identifier' => '/admin/*/rewrite/list/',
            'model_identifier'   => 'rewrite',
            'meta_title'         => 'Edit',
            'meta_description'   => '',
            'allow_params'       => 1,
        ],
        '/admin/*/rewrite/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'rewrite',
            'template'         => null,
        ],
    ],

    'block' => [
        'admin.menu' => [
            'tree' => [
                500 => [
                    'URL' => [
                        10 => [
                            'title' => 'Rewrites',
                            'path'  => '/admin/*/rewrite/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],

    'observer' => [
        'page_identifier_extract_before' => [
            'page_rewrite_match_identifier' => 200,
        ],
    ],

    'event' => [
        'page_rewrite_match_identifier' => [
            'class' => Rewrite_Observer_Rewrite::class,
        ],
    ],
];
