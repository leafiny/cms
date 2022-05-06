<?php

$config = [
    'page' => [
        '/product/ajax/add/*/' => [
            'class'    => AddToCart_Page_Product_Ajax_Add::class,
            'template' => null,
        ],
    ],

    'block' => [
        'ajax.cart.popup' => [
            'class'    => AddToCart_Block_Popup::class,
            'template' => 'Leafiny_AddToCart::block/popup.twig'
        ],
        'head' => [
            'stylesheet' => [
                'Leafiny_AddToCart::css/addtocart.css' => 700,
            ],
        ],
        'script' => [
            'javascript' => [
                'Leafiny_AddToCart::js/addtocart.js' => 200
            ],
        ],
    ]
];
