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
        'fpc_cache' => [
            'allowed_params' => [
                'blog' => Blog_Helper_Data::URL_PARAM_PAGE
            ],
        ]
    ],

    'block' => [
        'category.post.list' => [
            'template' => 'Leafiny_Blog::block/post/list.twig',
            'class'    => Blog_Block_Category_Post::class
        ],
        'blog.post.comments' => [
            'template' => 'Leafiny_Blog::block/post/comments.twig',
            'class'    => Blog_Block_Post_Comments::class
        ],

        'admin.head' => [
            'stylesheet' => [
                'Leafiny_Blog::backend/css/post.css' => 400,
            ],
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
        'admin.blog.form.editor' => [
            'template' => 'Leafiny_Editor::block/backend/form/editor.twig',
            'class'    => Editor_Block_Backend_Form_Editor::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'content',
            'label'    => 'Post',
            'actions'  => ['Markdown', 'HTML', 'Preview']
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
    ],

    'page' => [
        '/category/*.html' => [
            'children' => [
                'category.post.list' => 250,
            ]
        ],
        '/post/*.html' => [
            'class'   => Blog_Page_Post_View::class,
            'content' => 'Leafiny_Blog::page/view.twig',
        ],
        '/post/comment/post/' => [
            'class'              => Blog_Page_Post_Comment_Post::class,
            'template'           => null,
            'form_code_required' => true,
        ],

        /* Admin Pages */
        '/admin/*/posts/list/' => [
            'title'            => 'Posts',
            'template'         => 'Leafiny_Backend::page.twig',
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
            'template'           => 'Leafiny_Backend::page.twig',
            'class'              => Backend_Page_Admin_Form::class,
            'content'            => 'Leafiny_Blog::page/backend/posts/form.twig',
            'referer_identifier' => '/admin/*/posts/list/',
            'model_identifier'   => 'blog_post',
            'meta_title'         => 'Edit',
            'meta_description'   => '',
            'allow_params'       => 1,
            'javascript'         => [
                'Leafiny_Blog::backend/js/posts/form.js' => 100
            ]
        ],
        '/admin/*/posts/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'blog_post',
            'template'         => null,
        ],
        /* /Admin Pages */
    ],

    'observer' => [
        'frontend_page_post_process' => [
            'check_page_posts' => 150,
        ],
    ],

    'event' => [
        'check_page_posts' => [
            'class' => Blog_Observer_Page::class,
        ],
    ],
];
