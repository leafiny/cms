<?php

$config = [
    'model' => [
        'blog_post' => [
            'class' => Blog_Model_Post::class,
        ],
        'rewrite' => [
            'refresh' => [
                'blog_post' => [
                    'enabled' => 1,
                    'table'   => 'blog_post',
                    'column'  => 'path_key',
                    'source'  => '/*.html',
                    'target'  => '/post/*.html',
                ]
            ]
        ],
    ],

    'helper' => [
        'blog_post' => [
            'class'         => Blog_Helper_Data::class,
            'post_per_page' => 10,
        ],
    ],

    'block' => [
        'category.post.list' => [
            'template' => 'Leafiny_Blog::block/post/list.twig',
            'class'    => Blog_Block_Category_Post::class
        ],

        'admin.blog.form.categories' => [
            'disabled' => !class_exists('Category_Block_Backend_Form_Categories'),
            'template' => 'Leafiny_Category::block/backend/form/categories.twig',
            'class'    => 'Category_Block_Backend_Form_Categories',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'multiple' => 1,
            'name'     => 'category_ids',
            'label'    => 'Categories',
        ],
        'admin.blog.form.breadcrumb' => [
            'disabled' => !class_exists('Category_Block_Backend_Form_Categories'),
            'template' => 'Leafiny_Category::block/backend/form/categories.twig',
            'class'    => 'Category_Block_Backend_Form_Categories',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'breadcrumb',
            'label'    => 'Breadcrumb',
        ],
        'admin.menu' => [
            'tree' => [
                250 => [
                    'Blog' => [
                        10 => [
                            'title' => 'Posts',
                            'path'  => '/admin/*/posts/list/'
                        ],
                    ]
                ]
            ]
        ],
        'admin.script' => [
            'javascript' => [
                320 => 'Leafiny_Blog::backend/js/app.js'
            ]
        ],
    ],

    'page' => [
        '/category/*.html' => [
            'children' => [
                250 => 'category.post.list',
            ]
        ],
        '/post/*.html' => [
            'class'   => Blog_Page_Post_View::class,
            'content' => 'Leafiny_Blog::page/view.twig',
        ],

        /* Admin Pages */
        '/admin/*/posts/list/' => [
            'title'            => 'Posts',
            'template'         => 'Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Blog::page/backend/posts/list.twig',
            'model_identifier' => 'blog_post',
            'meta_title'       => 'Posts',
            'meta_description' => '',
        ],
        '/admin/*/posts/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'blog_post',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/posts/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/posts/edit/' => [
            'title'              => 'Edit',
            'template'           => 'Backend::page.twig',
            'class'              => Backend_Page_Admin_Form::class,
            'content'            => 'Leafiny_Blog::page/backend/posts/form.twig',
            'referer_identifier' => '/admin/*/posts/list/',
            'model_identifier'   => 'blog_post',
            'meta_title'         => 'Edit',
            'meta_description'   => '',
            'allow_params'       => 1,
        ],
        '/admin/*/posts/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'blog_post',
            'template'         => null,
        ],
        /* /Admin Pages */
    ],

    'observer' => [
        'page_identifier_extract_before' => [
            150 => 'page_rewrite_match_identifier_page',
        ],
    ],

    'event' => [
        'page_rewrite_match_identifier_page' => [
            'class' => Blog_Observer_Page::class,
        ],
    ],
];
