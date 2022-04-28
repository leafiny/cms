<?php

$config = [
    'model' => [
        'cms_block' => [
            'class' => Cms_Model_Block::class,
        ],
        'cms_page' => [
            'class' => Cms_Model_Page::class,
        ],
        'rewrite' => [
            'entity' => [
                'cms_page' => [
                    'enabled' => 1,
                    'table'   => 'cms_page',
                    'column'  => 'path_key',
                    'source'  => '/*.html',
                    'target'  => '/page/*.html',
                ]
            ]
        ],
    ],

    'helper' => [
        'cms' => [
            'class' => Cms_Helper_Cms::class,
        ],
        'search' => [
            'entity' => [
                'cms_page' => [
                    'enabled' => 0,
                    'columns' => [
                        'title' => 'title',
                    ],
                    'words' => [
                        'title' => 'title',
                    ],
                    'language' => 'language',
                    'block'    => 'search.cms.pages',
                    'position' => 300,
                ],
                'cms_block' => [
                    'enabled'  => 0,
                    'columns'  => [
                        'content' => 'content',
                    ],
                    'language' => 'language',
                    'block'    => 'search.cms.blocks',
                    'position' => 400,
                ],
            ],
        ],
    ],

    'block' => [
        'block.static::*' => [
            'template' => 'Leafiny_Cms::block/static/content.twig',
            'class'    => Cms_Block_Static_Content::class
        ],

        'category.cms.pages' => [
            'template' => 'Leafiny_Cms::block/category/pages.twig',
            'class'    => Cms_Block_Category_Pages::class
        ],
        'category.cms.blocks' => [
            'template' => 'Leafiny_Cms::block/category/blocks.twig',
            'class'    => Cms_Block_Category_Blocks::class
        ],

        'search.cms.pages' => [
            'template' => 'Leafiny_Cms::block/search/pages.twig',
            'class'    => Cms_Block_Search_Pages::class
        ],
        'search.cms.blocks' => [
            'template' => 'Leafiny_Cms::block/search/blocks.twig',
            'class'    => Cms_Block_Search_Blocks::class
        ],

        'cms.block.default' => [
            'template' => 'Leafiny_Cms::block/block/default.twig',
            'class'    => Cms_Block_Block_Default::class
        ],
        'cms.page.default' => [
            'template' => 'Leafiny_Cms::block/page/default.twig',
            'class'    => Cms_Block_Page_Default::class
        ],

        'admin.cms.form.categories' => [
            'disabled' => !class_exists('Category_Block_Backend_Form_Categories'),
            'template' => 'Leafiny_Category::block/backend/form/categories.twig',
            'class'    => 'Category_Block_Backend_Form_Categories',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'multiple' => 1,
            'name'     => 'category_ids',
            'label'    => 'Categories',
        ],
        'admin.cms.form.breadcrumb' => [
            'disabled' => !class_exists('Category_Block_Backend_Form_Categories'),
            'template' => 'Leafiny_Category::block/backend/form/categories.twig',
            'class'    => 'Category_Block_Backend_Form_Categories',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'breadcrumb',
            'label'    => 'Breadcrumb',
        ],
        'admin.cms.form.editor' => [
            'template' => 'Leafiny_Editor::block/backend/form/editor.twig',
            'class'    => Editor_Block_Backend_Form_Editor::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'content',
            'actions'  => ['Markdown', 'HTML', 'Preview']
        ],
        'admin.menu' => [
            'tree' => [
                200 => [
                    'Content' => [
                        10 => [
                            'title' => 'Pages',
                            'path'  => '/admin/*/pages/list/'
                        ],
                        20 => [
                            'title' => 'Blocks',
                            'path'  => '/admin/*/blocks/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],

    'page' => [
        '/category/*.html' => [
            'children' => [
                'category.cms.blocks' => 200,
                'category.cms.pages' => 300,
            ]
        ],
        '/page/*.html' => [
            'class'   => Cms_Page_Static_Content::class,
            'content' => 'Leafiny_Cms::page/content.twig',
            'cms_page_dynamic_metadata' => [
                'meta_title'       => '{{title}}',
                'meta_description' => '{{_category_1}} {{title}}',
            ],
        ],

        /* Admin Pages */
        '/admin/*/pages/list/' => [
            'title'            => 'Pages',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Cms::page/backend/pages/list.twig',
            'model_identifier' => 'cms_page',
            'meta_title'       => 'Pages',
            'meta_description' => '',
        ],
        '/admin/*/pages/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'cms_page',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/pages/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/pages/edit/' => [
            'title'              => 'Edit',
            'template'           => 'Leafiny_Backend::page.twig',
            'class'              => Backend_Page_Admin_Form::class,
            'content'            => 'Leafiny_Cms::page/backend/pages/form.twig',
            'referer_identifier' => '/admin/*/pages/list/',
            'model_identifier'   => 'cms_page',
            'meta_title'         => 'Edit',
            'meta_description'   => '',
            'allow_params'       => 1,
            'javascript' => [
                'Leafiny_Cms::backend/js/pages/form.js' => 100
            ]
        ],
        '/admin/*/pages/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'cms_page',
            'template'         => null,
        ],
        /* /Admin Pages */

        /* Admin Blocks */
        '/admin/*/blocks/list/' => [
            'title'            => 'Blocks',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Cms::page/backend/blocks/list.twig',
            'model_identifier' => 'cms_block',
            'meta_title'       => 'Blocks',
            'meta_description' => '',
        ],
        '/admin/*/blocks/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'cms_block',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/blocks/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/blocks/edit/' => [
            'title'              => 'Edit',
            'template'           => 'Leafiny_Backend::page.twig',
            'class'              => Backend_Page_Admin_Form::class,
            'content'            => 'Leafiny_Cms::page/backend/blocks/form.twig',
            'referer_identifier' => '/admin/*/blocks/list/',
            'model_identifier'   => 'cms_block',
            'meta_title'         => 'Edit',
            'meta_description'   => '',
            'allow_params'       => 1,
            'javascript' => [
                'Leafiny_Cms::backend/js/blocks/form.js' => 100
            ]
        ],
        '/admin/*/blocks/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'cms_block',
            'template'         => null,
        ],
        /* /Admin Blocks */
    ],
];
