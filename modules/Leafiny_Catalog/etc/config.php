<?php

$config = [
    'model' => [
        'catalog_product' => [
            'class' => Catalog_Model_Product::class,
        ],
        'rewrite' => [
            'entity' => [
                'catalog_product' => [
                    'enabled' => 1,
                    'table'   => 'catalog_product',
                    'column'  => 'path_key',
                    'source'  => '/*.html',
                    'target'  => '/product/*.html',
                ],
            ]
        ],
        'search_fulltext' => [
            'entity' => [
                'catalog_product' => [
                    'enabled'  => 1,
                    'columns'  => ['sku', 'name', 'description'],
                    'language' => 'language',
                    'block'    => 'search.products',
                    'position' => 100,
                ]
            ]
        ],
    ],

    'block' => [
        'category.products' => [
            'template' => 'Leafiny_Catalog::block/category/products.twig',
            'class'    => Catalog_Block_Category_Products::class
        ],
        'category.products.multipage' => [
            'template' => 'Leafiny_Catalog::block/category/products/multipage.twig',
            'class'    => Catalog_Block_Category_Products_Multipage::class
        ],

        'search.products' => [
            'template' => 'Leafiny_Catalog::block/search/products.twig',
            'class'    => Catalog_Block_Search_Products::class
        ],

        'catalog.product.default' => [
            'template' => 'Leafiny_Catalog::block/product/default.twig',
            'class'    => Catalog_Block_Product_Default::class,
        ],
        'catalog.product.price' => [
            'template' => 'Leafiny_Catalog::block/product/price.twig',
        ],

        'admin.catalog.product.form.categories' => [
            'disabled' => !class_exists('Category_Block_Backend_Form_Categories'),
            'template' => 'Leafiny_Category::block/backend/form/categories.twig',
            'class'    => 'Category_Block_Backend_Form_Categories',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'multiple' => 1,
            'name'     => 'category_ids',
            'label'    => 'Categories',
        ],
        'admin.catalog.product.form.breadcrumb' => [
            'disabled' => !class_exists('Category_Block_Backend_Form_Categories'),
            'template' => 'Leafiny_Category::block/backend/form/categories.twig',
            'class'    => 'Category_Block_Backend_Form_Categories',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'breadcrumb',
            'label'    => 'Breadcrumb',
        ],
        'admin.catalog.product.form.editor' => [
            'template' => 'Leafiny_Editor::block/backend/form/editor.twig',
            'class'    => Editor_Block_Backend_Form_Editor::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'description',
            'label'    => 'Description',
            'actions'  => ['Markdown', 'HTML', 'Preview']
        ],
        'admin.menu' => [
            'tree' => [
                300 => [
                    'Catalog' => [
                        10 => [
                            'title' => 'Products',
                            'path'  => '/admin/*/products/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],

    'page' => [
        '/category/*.html' => [
            'children' => [
                'category.products' => 100,
            ]
        ],
        '/product/*.html' => [
            'class'   => Catalog_Page_Product_View::class,
            'content' => 'Leafiny_Catalog::page/product/view.twig',
            'javascript' => [
                'Leafiny_Catalog::js/product.js' => 100
            ],
            'product_dynamic_metadata' => [
                'meta_title'       => '{{name}}',
                'meta_description' => '{{_category_1}} {{name}} {{sku}}',
            ],
        ],

        /* Admin Products */
        '/admin/*/products/list/' => [
            'title'            => 'Products',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Catalog_Page_Backend_Product_List::class,
            'content'          => 'Leafiny_Catalog::page/backend/products/list.twig',
            'model_identifier' => 'catalog_product',
            'meta_title'       => 'Products',
            'meta_description' => '',
        ],
        '/admin/*/products/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'catalog_product',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/products/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/products/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Catalog::page/backend/products/form.twig',
            'referer_identifier'    => '/admin/*/products/list/',
            'model_identifier'      => 'catalog_product',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
            'recommended_file_size' => '600x600',
            'javascript'            => [
                'Leafiny_Catalog::backend/js/products/form.js' => 100
            ]
        ],
        '/admin/*/products/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'catalog_product',
            'template'         => null,
        ],
        /* /Admin Products */
    ],

    'helper' => [
        'catalog_product' => [
            'class'            => Catalog_Helper_Data::class,
            'product_per_page' => 12,
        ],
        'fpc_cache' => [
            'allowed_params' => [
                'catalog' => Catalog_Helper_Data::URL_PARAM_PAGE
            ],
        ]
    ],

    'events' => [
        'frontend_page_post_process' => [
            'check_page_products' => 250,
        ],
    ],

    'observer' => [
        'check_page_products' => [
            'class' => Catalog_Observer_Page::class,
        ],
    ],
];
