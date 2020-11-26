<?php

$config = [
    'page' => [
        '/admin/*/trumbowyg/file/upload/' => [
            'class'    => Trumbowyg_Page_Backend_File_Upload::class,
            'template' => null,
        ],
        '/admin/*/js/trumbowyg.js' => [
            'class'    => Trumbowyg_Page_Backend_Js::class,
            'content'  => 'Leafiny_Trumbowyg::backend/page/script.js.twig',
            'template' => 'Leafiny_Trumbowyg::backend/blank.twig',
        ],
    ],

    'block' => [
        'admin.head' => [
            'stylesheet' => [
                410 => 'Leafiny_Trumbowyg::backend/css/trumbowyg/trumbowyg.min.css',
                420 => 'Leafiny_Trumbowyg::backend/css/style.css',
            ],
            'javascript' => [
                210 => 'Leafiny_Trumbowyg::backend/js/trumbowyg/trumbowyg.min.js',
                230 => 'Leafiny_Trumbowyg::backend/js/trumbowyg/plugins/upload/trumbowyg.upload.js',
            ],
            'children' => [
                210 => 'admin.head.trumbowyg',
            ],
        ],
        'admin.script' => [
            'javascript' => [
                210 => 'Leafiny_Trumbowyg::backend/js/app.js'
            ],
        ],
        'admin.head.trumbowyg' => [
            'template' => 'Leafiny_Trumbowyg::backend/block/include.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ]
    ]
];
