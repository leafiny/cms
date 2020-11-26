<?php

$config = [
    'app' => [
        'twig_filters' => [
            'translate'     => 'App::translate',
            'number_format' => 'number_format',
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
            'meta_description' => '',
            'banner_text'      => 'Welcome to Leafiny',
        ],
        '/page/*.html' => [
            'content' => 'Frontend::page/cms.twig', // Optional template override (Leafiny_Cms)
        ],
    ],

    'observer' => [
        'frontend_page_pre_process' => [
            100 => 'frontend_page_set_locale_information',
            200 => 'frontend_page_init_session',
        ],
    ],

    'event' => [
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
                100 => 'Frontend::css/pure-min.css',
                200 => 'Frontend::css/grids-responsive-min.css',
                300 => 'Frontend::css/style.css'
            ],
            'javascript' => [
                // Default theme use Vanilla Javascript
                // 100 => 'Frontend::js/jquery-3.5.1.min.js'
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
                'Frontend::js/app.js'
            ],
            'class' => Frontend_Block_Script::class
        ],
    ],
];
