<?php

$config = [
    'model' => [
        'cart_rule' => [
            'class' => CartRule_Model_Cart_Rule::class
        ],
        'cart_rule_coupon' => [
            'class' => CartRule_Model_Cart_Rule_Coupon::class
        ],
    ],

    'helper' => [
        'cart_rule' => [
            'class' => CartRule_Helper_Cart_Rule::class,
        ],
    ],

    'page' => [
        '/checkout/coupon/update/' => [
            'class'    => CartRule_Page_Checkout_Coupon_Update::class,
            'template' => null,
        ],
        '/checkout.html' => [
            'javascript' => [
                'Leafiny_CartRule::js/cart-rule.js' => 50,
            ]
        ],

        '/admin/*/cart-rules/list/' => [
            'title'            => 'Cart Rules',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => CartRule_Page_Backend_Rules_Cart_List::class,
            'content'          => 'Leafiny_CartRule::page/backend/rules/cart/list.twig',
            'model_identifier' => 'cart_rule',
            'meta_title'       => 'Cart Rules',
            'meta_description' => '',
        ],
        '/admin/*/cart-rules/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'cart_rule',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/cart-rules/new/' => [
            'title'              => 'New',
            'class'              => CartRule_Page_Backend_Rules_Cart_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/cart-rules/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => CartRule_Page_Backend_Rules_Cart_Form::class,
            'content'               => 'Leafiny_CartRule::page/backend/rules/cart/form.twig',
            'referer_identifier'    => '/admin/*/cart-rules/list/',
            'model_identifier'      => 'cart_rule',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
            'javascript'            => [
                'Leafiny_CartRule::backend/js/rules/cart/form.js' => 100
            ]
        ],
        '/admin/*/cart-rules/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'cart_rule',
            'template'         => null,
        ],
    ],

    'block' => [
        'head' => [
            'stylesheet' => [
                'Leafiny_CartRule::css/cart-rule.css' => 700,
            ],
        ],
        'commerce.checkout.coupon' => [
            'template' => 'Leafiny_CartRule::block/checkout/coupon.twig',
            'class'    => CartRule_Block_Checkout_Coupon::class
        ],

        'admin.head' => [
            'stylesheet' => [
                'Leafiny_CartRule::backend/css/cart-rule.css' => 400,
            ],
        ],
        'admin.rules.cart.condition' => [
            'template' => 'Leafiny_CartRule::block/backend/rules/cart/condition.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
        ],
        'admin.menu' => [
            'tree' => [
                800 => [
                    'Commerce' => [
                        15 => [
                            'title' => 'Cart Rules',
                            'path'  => '/admin/*/cart-rules/list/'
                        ],
                    ],
                ],
            ],
        ],
    ],

    'events' => [
        'order_complete_after' => [
            'cart_rule_increment_coupon' => 100,
        ],
        'collect_totals_before' => [
            'cart_rules_apply' => 100,
        ],
        'sale_update_item' => [
            'cart_rules_refresh_gift' => 100,
        ],
        'sale_shipping_method' => [
            'cart_rules_refresh_shipping' => 100,
        ],
    ],

    'observer' => [
        'cart_rule_increment_coupon' => [
            'class' => CartRule_Observer_Cart_Rule_IncrementCoupon::class
        ],
        'cart_rules_apply' => [
            'class' => CartRule_Observer_Cart_Rule_Apply::class
        ],
        'cart_rules_refresh_gift' => [
            'class' => CartRule_Observer_Cart_Rule_RefreshGift::class
        ],
        'cart_rules_refresh_shipping' => [
            'class' => CartRule_Observer_Cart_Rule_RefreshShipping::class
        ],
    ],
];
