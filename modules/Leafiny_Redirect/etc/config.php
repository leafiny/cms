<?php

$config = [
    'model' => [
        'redirect' => [
            'class' => Redirect_Model_Redirect::class
        ],
    ],

    'page' => [
        '/admin/*/redirect/list/' => [
            'title'            => 'Redirects',
            'template'         => 'Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Redirect::page/backend/redirect/list.twig',
            'model_identifier' => 'redirect',
            'meta_title'       => 'Redirects',
            'meta_description' => '',
        ],
        '/admin/*/redirect/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'redirect',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/redirect/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/redirect/edit/' => [
            'title'              => 'Edit',
            'template'           => 'Backend::page.twig',
            'class'              => Backend_Page_Admin_Form::class,
            'content'            => 'Leafiny_Redirect::page/backend/redirect/form.twig',
            'referer_identifier' => '/admin/*/redirect/list/',
            'model_identifier'   => 'redirect',
            'meta_title'         => 'Edit',
            'meta_description'   => '',
            'allow_params'       => 1,
        ],
        '/admin/*/redirect/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'redirect',
            'template'         => null,
        ],
    ],

    'block' => [
        'admin.menu' => [
            'tree' => [
                500 => [
                    'URL' => [
                        20 => [
                            'title' => 'Redirects',
                            'path'  => '/admin/*/redirect/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],

    'observer' => [
        'page_identifier_extract_before' => [
            100 => 'page_redirect_match_identifier',
        ],
    ],

    'event' => [
        'page_redirect_match_identifier' => [
            'class' => Redirect_Observer_Redirect::class,
        ],
    ],
];
