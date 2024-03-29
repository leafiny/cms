<?php

$config = [
    'app' => [
        'twig_filters' => [
            'attribute' => Attribute_Twig_Filters::class,
        ],
    ],

    'model' => [
        'attribute' => [
            'class' => Attribute_Model_Attribute::class,
        ],
    ],

    'helper' => [
        'attribute' => [
            'class'  => Attribute_Helper_Attribute::class,
            'entity' => [],
        ],
    ],

    'block' => [
        'head' => [
            'stylesheet' => [
                'Leafiny_Attribute::css/attribute.css' => 400,
            ],
        ],

        'category.products.filters' => [
            'class'    => Attribute_Block_Products_Filters::class,
            'template' => 'Leafiny_Attribute::block/filters.twig',
            'disabled' => !class_exists('Catalog_Helper_Catalog_Product'),
        ],
        'category.posts.filters' => [
            'class'    => Attribute_Block_Posts_Filters::class,
            'template' => 'Leafiny_Attribute::block/filters.twig',
            'disabled' => !class_exists('Blog_Helper_Blog_Post'),
        ],

        'category.attribute.list' => [
            'class'      => Attribute_Block_List::class,
            'template'   => 'Leafiny_Attribute::block/list.twig',
            'entity_key' => 'category',
        ],
        'product.attribute.list' => [
            'class'      => Attribute_Block_List::class,
            'template'   => 'Leafiny_Attribute::block/list.twig',
            'entity_key' => 'product',
        ],
        'post.attribute.list' => [
            'class'      => Attribute_Block_List::class,
            'template'   => 'Leafiny_Attribute::block/list.twig',
            'entity_key' => 'post',
        ],
        'page.attribute.list' => [
            'class'      => Attribute_Block_List::class,
            'template'   => 'Leafiny_Attribute::block/list.twig',
            'entity_key' => 'page',
        ],

        'admin.head' => [
            'stylesheet' => [
                'Leafiny_Attribute::backend/css/attribute.css' => 450,
            ],
        ],
        'admin.menu' => [
            'tree'     => [
                900 => [
                    'System' => [
                        5 => [
                            'title' => 'Attributes',
                            'path'  => '/admin/*/attribute/list/'
                        ],
                    ]
                ]
            ]
        ],
        'admin.attribute.product.form.attributes' => [
            'template' => 'Leafiny_Attribute::block/backend/form/attributes.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'class'    => Attribute_Block_Backend_Form_Attributes::class,
            'entity'   => 'catalog_product',
        ],
        'admin.attribute.category.form.attributes' => [
            'template' => 'Leafiny_Attribute::block/backend/form/attributes.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'class'    => Attribute_Block_Backend_Form_Attributes::class,
            'entity'   => 'category',
        ],
        'admin.attribute.post.form.attributes' => [
            'template' => 'Leafiny_Attribute::block/backend/form/attributes.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'class'    => Attribute_Block_Backend_Form_Attributes::class,
            'entity'   => 'blog_post',
        ],
        'admin.attribute.page.form.attributes' => [
            'template' => 'Leafiny_Attribute::block/backend/form/attributes.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'class'    => Attribute_Block_Backend_Form_Attributes::class,
            'entity'   => 'cms_page',
        ],
    ],

    'page' => [
        '/category/*.html' => [
            'children' => [
                'category.products.filters' => 95,
                'category.posts.filters'    => 245,
                'category.attribute.list' => 500,
            ]
        ],
        '/product/*.html' => [
            'children' => [
                'product.attribute.list' => 50,
            ]
        ],
        '/post/*.html' => [
            'children' => [
                'post.attribute.list' => 50,
            ]
        ],
        '/page/*.html' => [
            'children' => [
                'page.attribute.list' => 50,
            ]
        ],

        /* Forms */
        '/admin/*/products/edit/' => [
            'children' => [
                'admin.attribute.product.form.attributes' => 20,
            ],
        ],
        '/admin/*/categories/edit/' => [
            'children' => [
                'admin.attribute.category.form.attributes' => 20,
            ],
        ],
        '/admin/*/posts/edit/' => [
            'children' => [
                'admin.attribute.post.form.attributes' => 20,
            ],
        ],
        '/admin/*/pages/edit/' => [
            'children' => [
                'admin.attribute.page.form.attributes' => 20,
            ],
        ],

        /* Admin Attribute */
        '/admin/*/attribute/list/' => [
            'title'            => 'Attributes',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Attribute::page/backend/attribute/list.twig',
            'model_identifier' => 'attribute',
            'meta_title'       => 'Attributes',
            'meta_description' => '',
        ],
        '/admin/*/attribute/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'attribute',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/attribute/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/attribute/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Attribute::page/backend/attribute/form.twig',
            'referer_identifier'    => '/admin/*/attribute/list/',
            'model_identifier'      => 'attribute',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
            'javascript'            => [
                'Leafiny_Attribute::backend/js/attribute/form.js' => 200
            ]
        ],
        '/admin/*/attribute/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'attribute',
            'template'         => null,
        ],
        '/admin/*/attribute/option/delete/' => [
            'class'    => Attribute_Page_Backend_Attribute_Option_Delete::class,
            'template' => null,
        ],
        /* /Admin Attribute */
    ],

    'events' => [
        'catalog_product_get_after' => [
            'catalog_product_get_attributes' => 50,
        ],
        'catalog_product_save_after' => [
            'catalog_product_save_attributes' => 50,
        ],
        'category_get_after' => [
            'category_get_attributes' => 50,
        ],
        'category_save_after' => [
            'category_save_attributes' => 50,
        ],
        'blog_post_get_after' => [
            'blog_post_get_attributes' => 50,
        ],
        'blog_post_save_after' => [
            'blog_post_save_attributes' => 50,
        ],
        'cms_page_get_after' => [
            'cms_page_get_attributes' => 50,
        ],
        'cms_page_save_after' => [
            'cms_page_save_attributes' => 50,
        ],

        'frontend_page_post_process' => [
            'page_filters_apply' => 10,
        ],
    ],

    'observer' => [
        'catalog_product_get_attributes' => [
            'class'  => Attribute_Observer_Attributes_Get::class,
            'entity' => 'catalog_product',
        ],
        'catalog_product_save_attributes' => [
            'class'  => Attribute_Observer_Attributes_Save::class,
            'entity' => 'catalog_product',
        ],
        'category_get_attributes' => [
            'class'  => Attribute_Observer_Attributes_Get::class,
            'entity' => 'category',
        ],
        'category_save_attributes' => [
            'class'  => Attribute_Observer_Attributes_Save::class,
            'entity' => 'category',
        ],
        'blog_post_get_attributes' => [
            'class'  => Attribute_Observer_Attributes_Get::class,
            'entity' => 'blog_post',
        ],
        'blog_post_save_attributes' => [
            'class'  => Attribute_Observer_Attributes_Save::class,
            'entity' => 'blog_post',
        ],
        'cms_page_get_attributes' => [
            'class'  => Attribute_Observer_Attributes_Get::class,
            'entity' => 'cms_page',
        ],
        'cms_page_save_attributes' => [
            'class'  => Attribute_Observer_Attributes_Save::class,
            'entity' => 'cms_page',
        ],

        'page_filters_apply' => [
            'class' => Attribute_Observer_Filters_Apply::class,
        ],
    ],
];
