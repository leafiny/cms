<?php

$config = [
    'model' => [
        'gallery_image' => [
            'class' => Gallery_Model_Image::class,
        ],
        'gallery_group' => [
            'class' => Gallery_Model_Group::class,
        ],
    ],

    'block' => [
        'gallery::*' => [
            'template' => 'Leafiny_Gallery::block/static/gallery.twig',
            'class'    => Gallery_Block_Static_Gallery::class
        ],
        'category.galleries' => [
            'template' => 'Leafiny_Gallery::block/category/galleries.twig',
            'class'    => Gallery_Block_Category_Galleries::class
        ],

        'search.images' => [
            'template' => 'Leafiny_Gallery::block/search/images.twig',
            'class'    => Gallery_Block_Search_Images::class
        ],

        'gallery.image.default' => [
            'template' => 'Leafiny_Gallery::block/gallery/image.twig',
            'class'    => Gallery_Block_Gallery_Image::class
        ],

        'admin.head' => [
            'stylesheet' => [
                'Leafiny_Gallery::backend/css/gallery.css' => 600,
            ],
        ],
        'admin.script' => [
            'javascript' => [
                'Leafiny_Gallery::backend/js/gallery.js' => 200
            ]
        ],
        'admin.gallery.group.form.categories' => [
            'disabled' => !class_exists('Category_Block_Backend_Form_Categories'),
            'template' => 'Leafiny_Category::block/backend/form/categories.twig',
            'class'    => 'Category_Block_Backend_Form_Categories',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'multiple' => 1,
            'name'     => 'category_ids',
            'label'    => 'Categories',
        ],
        'admin.gallery.form' => [
            'template'          => 'Leafiny_Gallery::block/backend/form/gallery.twig',
            'class'             => Gallery_Block_Backend_Form_Gallery::class,
            'context'           => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'input_file_name'   => 'gallery_images',
            'input_data_name'   => 'gallery_image',
            'delete_identifier' => '/admin/*/gallery/image/delete/',
            'status_identifier' => '/admin/*/gallery/image/status/',
            'show_position'     => true,
            'show_label'        => true,
        ],
        'admin.menu' => [
            'tree' => [
                200 => [
                    'Content' => [
                        30 => [
                            'title' => 'Images',
                            'path'  => '/admin/*/gallery/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],

    'helper' => [
        'search' => [
            'entity' => [
                'gallery_image' => [
                    'enabled' => 0,
                    'columns' => [
                        'image' => 'image',
                        'label' => 'label',
                        'text'  => 'text',
                    ],
                    'block'    => 'search.images',
                    'position' => 500,
                ]
            ]
        ],
    ],

    'page' => [
        '/category/*.html' => [
            'children' => [
                'category.galleries' => 50,
            ]
        ],

        '/admin/*/gallery/image/delete/' => [
            'class'            => Gallery_Page_Backend_Gallery_Image_Delete::class,
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/gallery/image/status/' => [
            'class'            => Gallery_Page_Backend_Gallery_Image_Status::class,
            'template'         => null,
            'allow_params'     => 1,
        ],

        /* Gallery Groups */
        '/admin/*/gallery/list/' => [
            'title'            => 'Images',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Catalog_Page_Backend_Product_List::class,
            'content'          => 'Leafiny_Gallery::page/backend/gallery/list.twig',
            'model_identifier' => 'gallery_group',
            'meta_title'       => 'Images',
            'meta_description' => '',
        ],
        '/admin/*/gallery/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'gallery_group',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/gallery/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/gallery/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Gallery::page/backend/gallery/form.twig',
            'referer_identifier'    => '/admin/*/gallery/list/',
            'model_identifier'      => 'gallery_group',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
            'types'                 => ['folder' => 'Folder'],
            'javascript'            => [
                'Leafiny_Gallery::backend/js/gallery/form.js' => 200
            ]
        ],
        '/admin/*/gallery/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'gallery_group',
            'template'         => null,
        ],
        /* /Gallery Groups */
    ],

    'events' => [
        'backend_object_save_after' => [
            'backend_gallery_image_add' => 200,
        ],
        'object_delete_after' => [
            'backend_gallery_image_delete' => 200,
        ],
    ],

    'observer' => [
        'backend_gallery_image_add' => [
            'class' => Gallery_Observer_Backend_Gallery_ProcessImages::class,
            'allowed_extensions' => ['jpg', 'jpeg', 'gif', 'png'],
        ],
        'backend_gallery_image_delete' => [
            'class' => Gallery_Observer_Backend_Gallery_DeleteImages::class,
        ],
    ],
];
