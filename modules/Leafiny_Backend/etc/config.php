<?php

$config = [
    'app' => [
        'twig_filters' => [
            'backend' => Backend_Twig_Filters::class,
        ],
    ],

    'session' => [
        'backend' => [
            'class' => Backend_Session_Backend::class
        ]
    ],

    'page' => [
        '/admin/*/' => [
            'title'            => 'Dashboard',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Dashboard::class,
            'content'          => 'Leafiny_Backend::page/dashboard.twig',
            'meta_title'       => 'Dashboard',
            'meta_description' => '',
        ],

        /* Login */
        '/admin/*/login.html' => [
            'title'            => 'Login',
            'template'         => 'Leafiny_Backend::login.twig',
            'class'            => Backend_Page_Login_Form::class,
            'content'          => 'Leafiny_Backend::page/login/form.twig',
            'meta_title'       => 'Login',
            'meta_description' => '',
        ],
        '/admin/*/login/post/' => [
            'class'            => Backend_Page_Login_Form_Post::class,
            'template'         => null,
        ],
        /* /Login */

        /* User Account */
        '/admin/*/logout/' => [
            'class'            => Backend_Page_Account_Logout::class,
            'template'         => null,
        ],
        '/admin/*/account/' => [
            'title'            => 'Account',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Account_Form::class,
            'content'          => 'Leafiny_Backend::page/account/form.twig',
            'model_identifier' => 'admin_user',
            'meta_title'       => 'Account',
            'meta_description' => '',
            'allow_params'     => 1,
        ],
        '/admin/*/account/save/' => [
            'class'               => Backend_Page_Account_Form_Save::class,
            'model_identifier'    => 'admin_user',
            'redirect_identifier' => '/admin/*/account/',
            'template'            => null,
        ],
        /* /User Account */

        /* User Grid */
        '/admin/*/users/list/' => [
            'title'            => 'Users',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Backend::page/users/list.twig',
            'model_identifier' => 'admin_user',
            'meta_title'       => 'Users',
            'meta_description' => '',
        ],
        '/admin/*/users/list/action/' => [
            'class'            => Backend_Page_Users_List_Action::class,
            'model_identifier' => 'admin_user',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/users/new/' => [
            'title'            => 'New',
            'class'            => Backend_Page_Admin_Form_New::class,
            'template'         => null,
        ],
        '/admin/*/users/edit/' => [
            'title'              => 'Edit',
            'template'           => 'Leafiny_Backend::page.twig',
            'class'              => Backend_Page_Admin_Form::class,
            'content'            => 'Leafiny_Backend::page/users/form.twig',
            'referer_identifier' => '/admin/*/users/list/',
            'model_identifier'   => 'admin_user',
            'meta_title'         => 'Edit',
            'meta_description'   => '',
            'allow_params'       => 1,
            'javascript'         => [
                'Leafiny_Backend::js/users/form.js' => 100
            ],
        ],
        '/admin/*/users/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'admin_user',
            'template'         => null,
        ],
        /* /User Grid */

        /* Cache Management */
        '/admin/*/cache/list/' => [
            'title'            => 'Cache',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Cache_List::class,
            'content'          => 'Leafiny_Backend::page/cache/list.twig',
            'meta_title'       => 'Cache',
            'meta_description' => '',
            'default_limit'    => 100,
        ],
        '/admin/*/cache/flush/template/' => [
            'class'            => Backend_Page_Cache_Flush_Template::class,
            'template'         => null,
        ],
        '/admin/*/cache/flush/config/' => [
            'class'            => Backend_Page_Cache_Flush_Config::class,
            'template'         => null,
        ],
        '/admin/*/cache/flush/autoload/' => [
            'class'            => Backend_Page_Cache_Flush_Autoload::class,
            'template'         => null,
        ],
    ],

    'model' => [
        'admin_user' => [
            'class' => Backend_Model_Admin_User::class,
        ],
        'admin_user_resources' => [
            'class' => Backend_Model_Admin_User_Resources::class,
        ],
        'admin_cache' => [
            'class' => Backend_Model_Cache::class,
            'cache' => [
                'config' => [
                    'name'        => 'Configuration',
                    'description' => 'System configuration variables',
                    'flush'       => '/admin/*/cache/flush/config/',
                    'active'      => 'app.config_cache'
                ],
                'twig' => [
                    'name'        => 'Templates',
                    'description' => 'Twig compiled templates',
                    'flush'       => '/admin/*/cache/flush/template/',
                    'active'      => 'app.twig_cache'
                ],
                'autoload' => [
                    'name'        => 'Autoload',
                    'description' => 'Symlinks of module classes for autoload',
                    'flush'       => '/admin/*/cache/flush/autoload/',
                    'active'      => true
                ],
            ],
        ],
    ],

    'block' => [
        'admin.head' => [
            'template'   => 'Leafiny_Backend::block/head.twig',
            'context'    => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'charset'    => 'utf-8',
            'stylesheet' => [
                'Leafiny_Backend::css/pure-min.css' => 100,
                'Leafiny_Backend::css/grids-responsive-min.css' => 200,
                'Leafiny_Backend::css/leafiny.css' => 300,
            ],
            'class'      => Backend_Block_Head::class,
            'javascript' => [
                'Leafiny_Backend::js/backend.js' => 100
            ],
        ],
        'admin.menu' => [
            'template' => 'Leafiny_Backend::block/menu.twig',
            'class'    => Backend_Block_Menu::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'tree'     => [
                900 => [
                    'Admin' => [
                        10 => [
                            'title' => 'Users',
                            'path'  => '/admin/*/users/list/'
                        ],
                        30 => [
                            'title' => 'Cache',
                            'path'  => '/admin/*/cache/list/'
                        ],
                    ]
                ]
            ]
        ],
        'admin.account' => [
            'template' => 'Leafiny_Backend::block/account.twig',
            'class'    => Backend_Block_Account::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
        'admin.title' => [
            'template' => 'Leafiny_Backend::block/title.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
        'admin.message' => [
            'template' => 'Leafiny_Backend::block/message.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
        'admin.content' => [
            'template' => 'Leafiny_Backend::block/content.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
        'admin.footer' => [
            'template' => 'Leafiny_Backend::block/footer.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
        'admin.script' => [
            'template' => 'Leafiny_Backend::block/script.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'javascript' => [
                'Leafiny_Backend::js/grids.js' => 100,
                'Leafiny_Backend::js/form.js' => 100
            ],
            'class'    => Backend_Block_Script::class,
        ],
        'admin.list.toolbar' => [
            'template' => 'Leafiny_Backend::block/list/toolbar.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
        'admin.form.goto' => [
            'template' => 'Leafiny_Backend::block/form/goto.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
        'admin.user.edit.resources' => [
            'template' => 'Leafiny_Backend::block/users/edit/resources.twig',
            'class'    => Backend_Block_User_Edit_Resources::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ]
    ],

    'helper' => [
        'admin_data' => [
            'class' => Backend_Helper_Data::class,
        ],
    ],

    'events' => [
        'page_object_init_after' => [
            'backend_check_backend_key' => 100,
        ],
        'backend_page_pre_process' => [
            'backend_page_set_locale_information' => 100,
            'backend_page_init_session' => 200,
            'backend_check_user_is_allowed' => 300,
            'backend_object_key_warning' => 400,
            'backend_db_warning' => 500,
        ],
        'backend_page_process' => [
            'backend_check_user_name' => 100,
        ],
        'backend_object_save_after' => [
            'backend_user_save_resources' => 100,
        ]
    ],

    'observer' => [
        'backend_page_set_locale_information' => [
            'class' => Backend_Observer_SetLocaleInformation::class,
        ],
        'backend_page_init_session' => [
            'class' => Backend_Observer_InitSession::class,
        ],
        'backend_check_backend_key' => [
            'class' => Backend_Observer_CheckBackendKey::class,
        ],
        'backend_check_user_name' => [
            'class' => Backend_Observer_CheckUserName::class,
        ],
        'backend_check_user_is_allowed' => [
            'class' => Backend_Observer_CheckUserIsAllowed::class,
        ],
        'backend_object_key_warning' => [
            'class' => Backend_Observer_KeyWarning::class
        ],
        'backend_db_warning' => [
            'class' => Backend_Observer_DbWarning::class
        ],
        'backend_user_save_resources' => [
            'class' => Backend_Observer_User_SaveResources::class
        ],
    ],
];
