<?php

$config = [
    'block' => [
        'admin.head' => [
            'stylesheet' => [
                'Leafiny_Editor::backend/css/editor.css' => 500,
            ],
            'javascript' => [
                'Leafiny_Editor::backend/js/showdown.min.js' => 300,
            ]
        ],
        'admin.script' => [
            'javascript' => [
                'Leafiny_Editor::backend/js/editor.js' => 300
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
