<?php

$config = [
    'app' => [
        'twig_filters' => [
            'frontend' => Frontend_Twig_Filters::class,
        ],
    ],

    'session' => [
        'frontend' => [
            'class' => Frontend_Session_Frontend::class,
        ],
    ],

    'page' => [
        'default' => [
            'template' => 'Frontend::page.twig',
        ],
        'http_404' => [
            'title'            => 'Page Not Found',
            'class'            => Frontend_Page_Error::class,
            'content'          => 'Frontend::page/error.twig',
            'meta_title'       => 'Page Not Found',
            'meta_description' => '',
        ],
        '/' => [
            'title'            => 'Home',
            'content'          => 'Frontend::page/index.twig',
            'meta_title'       => 'Home',
            'meta_description' => 'Welcome to Leafiny!',
            'banner_text'      => 'Welcome to Leafiny',
        ],
    ],

    'events' => [
        'frontend_page_pre_process' => [
            'frontend_page_set_locale_information' => 100,
            'frontend_page_init_session' => 200,
        ],
    ],

    'observer' => [
        'frontend_page_set_locale_information' => [
            'class' => Frontend_Observer_SetLocaleInformation::class,
        ],
        'frontend_page_init_session' => [
            'class' => Frontend_Observer_InitSession::class,
        ],
    ],

    'block' => [
        'head' => [
            'template'   => 'Frontend::block/head.twig',
            'charset'    => 'utf-8',
            'stylesheet' => [
                'Frontend::css/pure-min.css' => 100,
                'Frontend::css/grids-responsive-min.css' => 200,
                'Frontend::css/style.css' => 300
            ],
            'javascript' => [
                // Load the necessary head scripts, Example:
                // 'Frontend::js/jquery-3.6.0.min.js' => 100
            ],
            'class' => Frontend_Block_Head::class
        ],
        'header' => [
            'template' => 'Frontend::block/header.twig',
        ],
        'breadcrumb' => [
            'template' => 'Frontend::block/breadcrumb.twig',
            'class' => Frontend_Block_Breadcrumb::class
        ],
        'banner' => [
            'template' => 'Frontend::block/banner.twig',
        ],
        'message' => [
            'template' => 'Frontend::block/message.twig',
        ],
        'content' => [
            'template' => 'Frontend::block/content.twig',
        ],
        'footer' => [
            'template' => 'Frontend::block/footer.twig',
        ],
        'script' => [
            'template' => 'Frontend::block/script.twig',
            'javascript' => [
                // Load the necessary scripts before body end
                'Frontend::js/app.js' => 100
            ],
            'class' => Frontend_Block_Script::class
        ],
    ],
];
