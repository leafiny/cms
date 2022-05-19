<?php

$config = [
    'model' => [
        'category' => [
            'class' => Category_Model_Category::class,
        ],
        'rewrite' => [
            'entity' => [
                'category' => [
                    'enabled' => 1,
                    'table'   => 'category',
                    'column'  => 'path_key',
                    'source'  => '/*.html',
                    'target'  => '/category/*.html',
                ]
            ]
        ],
    ],

    'helper' => [
        'category' => [
            'class' => Category_Helper_Category::class,
        ],
        'search' => [
            'entity' => [
                'category' => [
                    'enabled' => 0,
                    'columns' => [
                        'name'    => 'name',
                        'content' => 'content',
                    ],
                    'words' => [
                        'name' => 'name',
                    ],
                    'language' => 'language',
                    'block'    => 'search.categories',
                    'position' => 50,
                ],
            ],
        ],
        'attribute' => [
            'entity' => [
                'category' => [
                    'enabled' => 1,
                ],
            ]
        ],
    ],

    'block' => [
        'head' => [
            'stylesheet' => [
                'Leafiny_Category::css/menu.css' => 250
            ],
        ],
        'script' => [
            'javascript' => [
                'Leafiny_Category::js/menu.js' => 200
            ],
        ],

        'search.categories' => [
            'template' => 'Leafiny_Category::block/search/categories.twig',
            'class'    => Category_Block_Search_Categories::class
        ],

        'menu' => [
            'template' => 'Leafiny_Category::block/menu.twig',
            'class'    => Category_Block_Menu::class
        ],

        'admin.category.form.categories' => [
            'template' => 'Leafiny_Category::block/backend/form/categories.twig',
            'class'    => Category_Block_Backend_Form_Categories::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'parent_id',
            'label'    => 'Parent',
        ],
        'admin.category.form.editor' => [
            'template' => 'Leafiny_Editor::block/backend/form/editor.twig',
            'class'    => Editor_Block_Backend_Form_Editor::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'content',
        ],
        'admin.head' => [
            'javascript' => [
                'Leafiny_Category::backend/js/category.js' => 200
            ]
        ],
        'admin.menu' => [
            'tree' => [
                100 => [
                    'Menu' => [
                        10 => [
                            'title' => 'Categories',
                            'path'  => '/admin/*/categories/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],

    'page' => [
        '/category/*.html' => [
            'class'        => Category_Page_Category_View::class,
            'content'      => 'Leafiny_Category::page/category/view.twig',
            'allow_params' => 1,
            'category_dynamic_metadata' => [
                'meta_title'       => '{{name}}',
                'meta_description' => '{{_category_1}} {{_category_2}} {{_category_3}}',
            ],
        ],

        /* Admin Categories */
        '/admin/*/categories/list/' => [
            'title'            => 'Categories',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Category::page/backend/categories/list.twig',
            'model_identifier' => 'category',
            'meta_title'       => 'Categories',
            'meta_description' => '',
        ],
        '/admin/*/categories/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'category',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/categories/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/categories/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Category::page/backend/categories/form.twig',
            'referer_identifier'    => '/admin/*/categories/list/',
            'model_identifier'      => 'category',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
            'recommended_file_size' => '940x200',
            'max_file_number'       => 1,
            'javascript' => [
                'Leafiny_Category::backend/js/categories/form.js' => 100
            ]
        ],
        '/admin/*/categories/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'category',
            'template'         => null,
        ],
        /* /Admin Categories */
    ],
];
