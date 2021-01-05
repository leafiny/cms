<?php

$config = [
    'model' => [
        'gallery_image' => [
            'class' => Gallery_Model_Image::class,
        ],
    ],

    'block' => [
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
        'admin.gallery.form' => [
            'template'          => 'Leafiny_Gallery::block/backend/form/gallery.twig',
            'class'             => Gallery_Block_Backend_Form_Gallery::class,
            'context'           => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'input_file_name'   => 'gallery_images',
            'input_data_name'   => 'gallery_image',
            'delete_identifier' => '/admin/*/gallery/image/delete/',
            'status_identifier' => '/admin/*/gallery/image/status/',
        ],
    ],

    'page' => [
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
    ],

    'observer' => [
        'backend_object_save_after' => [
            'backend_gallery_image_add' => 200,
        ],
        'object_delete_after' => [
            'backend_gallery_image_delete' => 200,
        ],
    ],

    'event' => [
        'backend_gallery_image_add' => [
            'class' => Gallery_Observer_Backend_Gallery_ProcessImages::class,
            'allowed_extensions' => ['jpg', 'jpeg', 'gif', 'png'],
        ],
        'backend_gallery_image_delete' => [
            'class' => Gallery_Observer_Backend_Gallery_DeleteImages::class,
        ],
    ],
];
