<?php

$config = [
    'model' => [
        'blog_post' => [
            'class' => Blog_Model_Post::class,
        ],
        'rewrite' => [
            'entity' => [
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
            'class'         => Blog_Helper_Blog_Post::class,
            'item_per_page' => 10,
        ],
        'fpc_cache' => [
            'allowed_params' => [
                'blog' => Blog_Helper_Blog_Post::URL_PARAM_PAGE
            ],
        ],
        'attribute' => [
            'entity' => [
                'blog_post' => [
                    'enabled' => 1,
                    'helper'  => 'blog_post',
                ],
            ]
        ],
        'search' => [
            'entity' => [
                'blog_post' => [
                    'enabled' => 0,
                    'columns' => [
                        'title'  => 'title',
                        'intro'  => 'intro',
                        'author' => 'author'
                    ],
                    'words' => [
                        'title' => 'title'
                    ],
                    'language' => 'language',
                    'block'    => 'search.posts',
                    'position' => 200,
                ]
            ]
        ],
    ],

    'block' => [
        'category.posts' => [
            'template' => 'Leafiny_Blog::block/category/posts.twig',
            'class'    => Blog_Block_Category_Posts::class
        ],
        'category.posts.multipage' => [
            'template' => 'Leafiny_Blog::block/category/posts/multipage.twig',
            'class'    => Blog_Block_Category_Posts_Multipage::class
        ],

        'search.posts' => [
            'template' => 'Leafiny_Blog::block/search/posts.twig',
            'class'    => Blog_Block_Search_Posts::class
        ],

        'blog.post.default' => [
            'template' => 'Leafiny_Blog::block/post/default.twig',
            'class'    => Blog_Block_Post_Default::class
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
                'category.posts' => 250,
            ]
        ],
        '/post/*.html' => [
            'class'   => Blog_Page_Post_View::class,
            'content' => 'Leafiny_Blog::page/view.twig',
            'blog_post_dynamic_metadata' => [
                'meta_title'       => '{{title}}',
                'meta_description' => '{{_category_1}} {{title}}',
            ],
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

    'events' => [
        'frontend_page_post_process' => [
            'check_page_posts' => 150,
        ],
    ],

    'observer' => [
        'check_page_posts' => [
            'class' => Blog_Observer_Page::class,
        ],
    ],
];
