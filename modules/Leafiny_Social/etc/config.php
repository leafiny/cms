<?php

$config = [
    'model' => [
        'social_comment' => [
            'class'          => Social_Model_Comment::class,
            'default_status' => 0,
        ],
    ],

    'helper' => [
        'social_comment' => [
            'class' => Social_Helper_Comment::class,
        ],
    ],

    'block' => [
        'head' => [
            'stylesheet' => [
                'Leafiny_Social::css/comment.css' => 800,
            ],
        ],

        'social.comments.content' => [
            'template' => 'Leafiny_Social::block/comments/content.twig',
            'class'    => Social_Block_Comment::class
        ],

        'blog.post.comments' => [
            'template'    => 'Leafiny_Social::block/comments.twig',
            'entity_type' => 'blog_post',
            'entity_key'  => 'post'
        ],

        'admin.menu' => [
            'tree' => [
                280 => [
                    'Social' => [
                        10 => [
                            'title' => 'Comments',
                            'path'  => '/admin/*/comment/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],

    'page' => [
        '/social/comment/post/' => [
            'class'              => Social_Page_Comment_Post::class,
            'template'           => null,
            'form_code_required' => true,
        ],

        '/post/*.html' => [
            'children' => [
                'blog.post.comments' => 100,
            ],
        ],

        /* Admin Comment */
        '/admin/*/comment/list/' => [
            'title'            => 'Comments',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Social::page/backend/comment/list.twig',
            'model_identifier' => 'social_comment',
            'meta_title'       => 'Comments',
            'meta_description' => '',
            'list_buttons'     => [],
        ],
        '/admin/*/comment/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'social_comment',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/comment/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/comment/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Social::page/backend/comment/form.twig',
            'referer_identifier'    => '/admin/*/comment/list/',
            'model_identifier'      => 'social_comment',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
        ],
        '/admin/*/comment/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'social_comment',
            'template'         => null,
        ],
        /* /Admin Comment */
    ],

    'mail' => [
        'new_comment' => [
            'subject'  => 'New comment',
            'template' => 'Leafiny_Social::mail/comment/new.twig',
        ]
    ],
];
