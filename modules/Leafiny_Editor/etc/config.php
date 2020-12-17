<?php

$config = [
    'block' => [
        'admin.head' => [
            'stylesheet' => [
                710 => 'Leafiny_Editor::backend/css/editor.css',
            ],
            'javascript' => [
                710 => 'Leafiny_Editor::backend/js/showdown.min.js',
            ]
        ],
        'admin.script' => [
            'javascript' => [
                710 => 'Leafiny_Editor::backend/js/editor.js'
            ],
        ],
        'admin.default.form.editor' => [
            'template' => 'Leafiny_Editor::block/backend/form/editor.twig',
            'class'    => Editor_Block_Backend_Form_Editor::class,
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'name'     => 'content',
            'actions'  => ['Markdown', 'HTML', 'Preview']
        ],
    ]
];
