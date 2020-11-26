<?php

$config = [
    'model' => [
        'log_db' => [
            'class' => Log_Model_Db::class
        ],
        'log_file' => [
            'class' => Log_Model_File::class
        ],
    ],

    'page' => [
        /* Admin */
        '/admin/*/log/list/' => [
            'title'            => 'Logs',
            'template'         => 'Backend::page.twig',
            'class'            => Log_Page_Backend_Log_List::class,
            'content'          => 'Leafiny_Log::page/backend/log/list.twig',
            'model_identifier' => 'log_db',
            'meta_title'       => 'Messages',
            'meta_description' => '',
            'list_buttons'     => []
        ],
        '/admin/*/log/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'log_db',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/log/edit/' => [
            'title'              => 'Log',
            'template'           => 'Backend::page.twig',
            'class'              => Log_Page_Backend_Log_View::class,
            'content'            => 'Leafiny_Log::page/backend/log/view.twig',
            'referer_identifier' => '/admin/*/log/list/',
            'model_identifier'   => 'log_db',
            'meta_title'         => 'Log',
            'meta_description'   => '',
            'allow_params'       => 1,
        ],
        /* /Admin */
    ],

    'observer' => [
        'backend_user_connect' => [
            1000 => 'log_backend_user_connect',
        ],
        'backend_object_save_after' => [
            1000 => 'log_object_save_after',
        ],
        'backend_action_remove_delete_after' => [
            1000 => 'log_backend_action_remove_delete_after',
        ]
    ],

    'event' => [
        'log_backend_user_connect' => [
            'class' => Log_Observer_Backend_Connect::class,
        ],
        'log_object_save_after' => [
            'class' => Log_Observer_Backend_Entity_Save::class,
        ],
        'log_backend_action_remove_delete_after' => [
            'class' => Log_Observer_Backend_Entity_Delete::class,
        ]
    ],

    'block' => [
        'admin.menu' => [
            'tree' => [
                900 => [
                    'Admin' => [
                        20 => [
                            'title' => 'Logs',
                            'path'  => '/admin/*/log/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],
];