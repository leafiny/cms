<?php

$config = [
    'model' => [
        'contact_message' => [
            'class' => Contact_Model_Message::class,
        ],
    ],

    'page' => [
        /* Frontend */
        '/contact.html' => [
            'class'            => Contact_Page_Contact::class,
            'title'            => 'Contact',
            'content'          => 'Leafiny_Contact::page/contact.twig',
            'meta_title'       => 'Contact',
            'meta_description' => '',
            'canonical'        => 'contact.html',
        ],
        '/contact/post/' => [
            'class'    => Contact_Page_Contact_Post::class,
            'template' => null,
        ],
        /* /Frontend */

        /* Admin */
        '/admin/*/messages/list/' => [
            'title'            => 'Messages',
            'template'         => 'Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Contact::page/backend/message/list.twig',
            'model_identifier' => 'contact_message',
            'meta_title'       => 'Messages',
            'meta_description' => '',
            'list_buttons'     => []
        ],
        '/admin/*/messages/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'contact_message',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/messages/edit/' => [
            'title'              => 'Message',
            'template'           => 'Backend::page.twig',
            'class'              => Contact_Page_Backend_Message_View::class,
            'content'            => 'Leafiny_Contact::page/backend/message/view.twig',
            'referer_identifier' => '/admin/*/messages/list/',
            'model_identifier'   => 'contact_message',
            'meta_title'         => 'Message',
            'meta_description'   => '',
            'allow_params'       => 1,
        ],
        /* /Admin */
    ],

    'mail' => [
        'contact' => [
            'subject'  => 'New contact message',
            'template' => 'Leafiny_Contact::mail/contact.twig',
        ]
    ],

    'block' => [
        'contact.form' => [
            'template' => 'Leafiny_Contact::block/form.twig',
        ],
        'admin.menu' => [
            'tree' => [
                400 => [
                    'Contact' => [
                        10 => [
                            'title' => 'Messages',
                            'path'  => '/admin/*/messages/list/'
                        ],
                    ]
                ]
            ]
        ],
    ]
];
