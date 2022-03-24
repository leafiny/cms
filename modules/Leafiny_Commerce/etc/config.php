<?php

$config = [
    'app' => [
        'twig_filters' => [
            'commerce' => Commerce_Twig_Filters::class,
        ],
    ],

    'model' => [
        'sale' => [
            'class' => Commerce_Model_Sale::class,
        ],
        'sale_address' => [
            'class' => Commerce_Model_Sale_Address::class,
        ],
        'sale_item' => [
            'class' => Commerce_Model_Sale_Item::class
        ],
        'sale_history' => [
            'class' => Commerce_Model_Sale_History::class
        ],
        'sale_status' => [
            'class' => Commerce_Model_Sale_Status::class
        ],
        'tax' => [
            'class' => Commerce_Model_Tax::class
        ],
        'tax_rule' => [
            'class' => Commerce_Model_Tax_Rule::class
        ],
        'shipping' => [
            'class' => Commerce_Model_Shipping::class
        ],
        'shipping_price' => [
            'class' => Commerce_Model_Shipping_Price::class
        ],
        'cart_rule' => [
            'class' => Commerce_Model_Cart_Rule::class
        ],
        'cart_rule_coupon' => [
            'class' => Commerce_Model_Cart_Rule_Coupon::class
        ],
    ],

    'helper' => [
        'checkout' => [
            'class' => Commerce_Helper_Checkout::class,
            'steps' => [
                'cart' => [
                    'position' => 100,
                    'label'    => 'Cart',
                    'enabled'  => true,
                ],
                'addresses' => [
                    'position' => 200,
                    'label'    => 'Addresses',
                    'enabled'  => true,
                ],
                'shipping' => [
                    'position' => 300,
                    'label'    => 'Shipping',
                    'enabled'  => true,
                ],
                'payment' => [
                    'position' => 400,
                    'label'    => 'Payment',
                    'enabled'  => true,
                ],
                'review' => [
                    'position' => 500,
                    'label'    => 'Order Review',
                    'enabled'  => true,
                ],
            ]
        ],
        'tax' => [
            'class' => Commerce_Helper_Tax::class,
            'default_country_code' => '*',
            'default_state_code' => '*',
            'default_postcode' => '*',
        ],
        'cart' => [
            'class' => Commerce_Helper_Cart::class,
            'currency' => '$',
            'agreements_url' => 'agreements.html',
        ],
        'cart_rule' => [
            'class' => Commerce_Helper_Cart_Rule::class,
            'cart_rule_allowed_types' => [
                Commerce_Model_Cart_Rule::TYPE_PERCENT_SUBTOTAL   => 'Percent Subtotal',
                Commerce_Model_Cart_Rule::TYPE_PERCENT_SHIPPING   => 'Percent Shipping',
                Commerce_Model_Cart_Rule::TYPE_AMOUNT_PER_PRODUCT => 'Amount per product',
            ],
        ],
        'shipping' => [
            'class' => Commerce_Helper_Shipping::class,
            'allowed_countries' => [
                'US',
            ],
            'default_country_code' => 'US',
            'default_state_code'   => '',
            'default_postcode'     => '',
            'default_price_type'   => 'excl_tax',
            'default_tax_rule_id'  => 1,
            'package_weight'       => 0,
        ],
        'payment' => [
            'class' => Commerce_Helper_Payment::class,
            'payment_methods' => [],
        ],
        'order' => [
            'class'                => Commerce_Helper_Order::class,
            'sale_increment_id'    => 'OR1000001',
            'invoice_increment_id' => 'IN1000001',
        ],
        'commerce_product' => [
            'class'               => Commerce_Helper_Product::class,
            'default_price_type'  => 'excl_tax',
            'default_tax_rule_id' => 1,
        ],
        'invoice' => [
            'class'  => Commerce_Helper_Invoice::class,
            'fonts'  => 'Leafiny_Commerce::pdf/fonts',
            'logo'   => 'Leafiny_Commerce::pdf/logo.png',
            'seller' => [
                'company'  => 'Leafiny',
                'street'   => 'XX XXX XX XX XXXXXXXXXXXX',
                'postcode' => '00000',
                'city'     => 'XXXXXXXXX',
                'email'    => 'XXXXXXX@XXXXXXXX.com',
            ],
            'legal' => [],
            'info' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'date' => '%d %B %Y'
        ]
    ],

    'page' => [
        '/product/add/*/' => [
            'class'    => Commerce_Page_Product_Add::class,
            'template' => null,
        ],
        '/checkout.html' => [
            'class'            => Commerce_Page_Checkout::class,
            'title'            => 'Your Cart',
            'content'          => 'Leafiny_Commerce::page/checkout.twig',
            'meta_title'       => 'Your Cart',
            'meta_description' => '',
            'canonical'        => 'checkout.html',
            'hide_minicart'    => false,
            'robots'           => 'NOINDEX,NOFOLLOW',
            'javascript'       => [
                'Leafiny_Commerce::js/checkout.js' => 100,
                'Leafiny_Commerce::js/checkout/ajax.js' => 200
            ]
        ],

        '/checkout/item/remove/*/' => [
            'class'    => Commerce_Page_Product_Remove::class,
            'template' => null,
        ],
        '/checkout/items/update/' => [
            'class'    => Commerce_Page_Checkout_Items_Update::class,
            'template' => null,
        ],
        '/checkout/coupon/update/' => [
            'class'    => Commerce_Page_Checkout_Coupon_Update::class,
            'template' => null,
        ],

        '/checkout/order/place/' => [
            'class'    => Commerce_Page_Order_Place::class,
            'template' => null,
        ],
        '/checkout/order/complete/' => [
            'class'            => Commerce_Page_Order_Complete::class,
            'title'            => 'Order Confirmation',
            'content'          => 'Leafiny_Commerce::page/complete.twig',
            'meta_title'       => 'Order Confirmation',
            'meta_description' => '',
            'allow_params'     => 1,
            'robots'           => 'NOINDEX,NOFOLLOW',
        ],
        '/order/progress/*/' => [
            'class'            => Commerce_Page_Order_Progress::class,
            'title'            => 'Order Progress',
            'template'         => 'Leafiny_Commerce::progress.twig',
            'content'          => 'Leafiny_Commerce::page/progress/content.twig',
            'meta_title'       => 'Order Progress',
            'meta_description' => '',
            'robots'           => 'NOINDEX,NOFOLLOW',
        ],

        /* Product admin form */
        '/admin/*/products/edit/' => [
            'children' => [
                'admin.commerce.product.form.additional' => 10,
            ],
        ],

        /* Admin Taxes */
        '/admin/*/taxes/list/' => [
            'title'            => 'Taxes',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Commerce::page/backend/taxes/list.twig',
            'model_identifier' => 'tax',
            'meta_title'       => 'Taxes',
            'meta_description' => '',
            'list_buttons'     => [
                '/admin/*/tax-rules/list/' => 'Rules',
                '/admin/*/taxes/new/'   => 'Add',
            ]
        ],
        '/admin/*/taxes/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'tax',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/taxes/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/taxes/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Commerce::page/backend/taxes/form.twig',
            'referer_identifier'    => '/admin/*/taxes/list/',
            'model_identifier'      => 'tax',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
        ],
        '/admin/*/taxes/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'tax',
            'template'         => null,
        ],
        /* /Admin Taxes */

        /* Admin Tax Rules */
        '/admin/*/tax-rules/list/' => [
            'title'            => 'Tax Rules',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Commerce::page/backend/taxes/rules/list.twig',
            'model_identifier' => 'tax_rule',
            'meta_title'       => 'Taxes',
            'meta_description' => '',
            'list_buttons'     => [
                '/admin/*/taxes/list/' => 'Taxes',
                '/admin/*/tax-rules/new/'   => 'Add',
            ]
        ],
        '/admin/*/tax-rules/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'tax_rule',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/tax-rules/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/tax-rules/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Commerce::page/backend/taxes/rules/form.twig',
            'referer_identifier'    => '/admin/*/tax-rules/list/',
            'model_identifier'      => 'tax_rule',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
        ],
        '/admin/*/tax-rules/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'tax_rule',
            'template'         => null,
        ],
        /* /Admin Rules */

        /* Admin Shipping */
        '/admin/*/shipping/list/' => [
            'title'            => 'Shipping',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Commerce::page/backend/shipping/list.twig',
            'model_identifier' => 'shipping',
            'meta_title'       => 'Shipping',
            'meta_description' => '',
        ],
        '/admin/*/shipping/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'shipping',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/shipping/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/shipping/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Commerce::page/backend/shipping/form.twig',
            'referer_identifier'    => '/admin/*/shipping/list/',
            'model_identifier'      => 'shipping',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
        ],
        '/admin/*/shipping/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'shipping',
            'template'         => null,
        ],
        /* /Admin Shipping */

        /* Admin Status */
        '/admin/*/status/list/' => [
            'title'            => 'Statuses',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Commerce::page/backend/status/list.twig',
            'model_identifier' => 'sale_status',
            'meta_title'       => 'Statuses',
            'meta_description' => '',
        ],
        '/admin/*/status/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'sale_status',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/status/new/' => [
            'title'              => 'New',
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/status/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Backend_Page_Admin_Form::class,
            'content'               => 'Leafiny_Commerce::page/backend/status/form.twig',
            'referer_identifier'    => '/admin/*/status/list/',
            'model_identifier'      => 'sale_status',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
        ],
        '/admin/*/status/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'sale_status',
            'template'         => null,
        ],
        /* /Admin Shipping */

        /* Admin Orders */
        '/admin/*/orders/list/' => [
            'title'            => 'Orders',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Commerce_Page_Backend_Order_List::class,
            'content'          => 'Leafiny_Commerce::page/backend/orders/list.twig',
            'model_identifier' => 'sale',
            'meta_title'       => 'Orders',
            'meta_description' => '',
            'list_buttons'     => [],
            'list_actions'     => [],
            'permanent_filters' => [
                [
                    'column' => 'state',
                    'value'  => Commerce_Model_Sale::SALE_STATE_ORDER,
                ]
            ],
        ],
        '/admin/*/orders/list/action/' => [
            'class'            => Backend_Page_Admin_List_Action::class,
            'model_identifier' => 'sale',
            'template'         => null,
            'allow_params'     => 1,
        ],
        '/admin/*/orders/edit/' => [
            'title'                 => 'Order',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Commerce_Page_Backend_Order_View::class,
            'content'               => 'Leafiny_Commerce::page/backend/orders/view.twig',
            'referer_identifier'    => '/admin/*/orders/list/',
            'model_identifier'      => 'sale',
            'meta_title'            => 'Order',
            'meta_description'      => '',
            'allow_params'          => 1,
            'javascript'            => [
                'Leafiny_Commerce::backend/js/orders/view.js' => 100
            ]
        ],
        '/admin/*/order/history/post/' => [
            'class'    => Commerce_Page_Backend_Order_History_Post::class,
            'template' => null,
        ],
        '/admin/*/order/shipment/post/' => [
            'class'    => Commerce_Page_Backend_Order_Shipment_Post::class,
            'template' => null,
        ],
        '/admin/*/order/invoice/download/' => [
            'class'        => Commerce_Page_Backend_Order_Invoice_Download::class,
            'template'     => null,
            'allow_params' => 1,
        ],
        '/admin/*/order/invoice/create/' => [
            'class'        => Commerce_Page_Backend_Order_Invoice_Create::class,
            'template'     => null,
            'allow_params' => 1,
        ],

        /* Cart Rules */
        '/admin/*/cart-rules/list/' => [
            'title'            => 'Cart Rules',
            'template'         => 'Leafiny_Backend::page.twig',
            'class'            => Backend_Page_Admin_List::class,
            'content'          => 'Leafiny_Commerce::page/backend/rules/cart/list.twig',
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
            'class'              => Backend_Page_Admin_Form_New::class,
            'template'           => null,
        ],
        '/admin/*/cart-rules/edit/' => [
            'title'                 => 'Edit',
            'template'              => 'Leafiny_Backend::page.twig',
            'class'                 => Commerce_Page_Backend_Rules_Cart_Form::class,
            'content'               => 'Leafiny_Commerce::page/backend/rules/cart/form.twig',
            'referer_identifier'    => '/admin/*/cart-rules/list/',
            'model_identifier'      => 'cart_rule',
            'meta_title'            => 'Edit',
            'meta_description'      => '',
            'allow_params'          => 1,
        ],
        '/admin/*/cart-rules/edit/save/' => [
            'class'            => Backend_Page_Admin_Form_Save::class,
            'model_identifier' => 'cart_rule',
            'template'         => null,
        ],
        /* /Admin Shipping */
    ],

    'block' => [
        'head' => [
            'stylesheet' => [
                'Leafiny_Commerce::css/commerce.css' => 600
            ],
        ],

        'catalog.product.price' => [
            'template' => 'Leafiny_Commerce::block/product/price.twig',
        ],
        'commerce.add.to.cart' => [
            'template' => 'Leafiny_Commerce::block/product/addtocart.twig',
            'class'    => Commerce_Block_AddToCart::class
        ],

        'commerce.mini.cart' => [
            'template' => 'Leafiny_Commerce::block/minicart.twig',
        ],
        'commerce.mini.cart.content' => [
            'template' => 'Leafiny_Commerce::block/minicart/content.twig',
            'class'    => Commerce_Block_MiniCart::class,
        ],

        'commerce.checkout' => [
            'template' => 'Leafiny_Commerce::block/checkout.twig',
            'class'    => Commerce_Block_Checkout::class,
        ],

        'commerce.checkout.cart' => [
            'template'  => 'Leafiny_Commerce::block/checkout/cart.twig',
            'class'     => Commerce_Block_Checkout_Cart::class,
        ],
        'commerce.checkout.cart.items' => [
            'template' => 'Leafiny_Commerce::block/checkout/cart/items.twig',
            'class'    => Commerce_Block_Checkout_Cart_Items::class
        ],
        'commerce.checkout.cart.subtotal' => [
            'template' => 'Leafiny_Commerce::block/checkout/cart/subtotal.twig',
            'class'    => Commerce_Block_Checkout_Cart_Subtotal::class
        ],

        'commerce.checkout.addresses' => [
            'template'  => 'Leafiny_Commerce::block/checkout/addresses.twig',
            'class'     => Commerce_Block_Checkout_Addresses::class,
        ],
        'commerce.checkout.addresses.shipping' => [
            'template' => 'Leafiny_Commerce::block/checkout/addresses/shipping.twig',
            'class'    => Commerce_Block_Checkout_Addresses_Shipping::class
        ],
        'commerce.checkout.addresses.billing' => [
            'template' => 'Leafiny_Commerce::block/checkout/addresses/billing.twig',
            'class'    => Commerce_Block_Checkout_Addresses_Billing::class
        ],

        'commerce.checkout.shipping' => [
            'template'  => 'Leafiny_Commerce::block/checkout/shipping.twig',
            'class'     => Commerce_Block_Checkout_Shipping::class,
        ],

        'commerce.checkout.payment' => [
            'template'  => 'Leafiny_Commerce::block/checkout/payment.twig',
            'class'     => Commerce_Block_Checkout_Payment::class,
        ],

        'commerce.checkout.review' => [
            'template' => 'Leafiny_Commerce::block/checkout/review.twig',
            'class'    => Commerce_Block_Checkout_Review::class
        ],
        'commerce.checkout.review.coupon' => [
            'template' => 'Leafiny_Commerce::block/checkout/review/coupon.twig',
            'class'    => Commerce_Block_Checkout_Review_Coupon::class
        ],
        'commerce.checkout.review.totals' => [
            'template' => 'Leafiny_Commerce::block/checkout/review/totals.twig',
            'class'    => Commerce_Block_Checkout_Review_Totals::class
        ],
        'commerce.checkout.review.info' => [
            'template' => 'Leafiny_Commerce::block/checkout/review/info.twig',
            'class'    => Commerce_Block_Checkout_Review_Info::class
        ],
        'commerce.checkout.review.agreements' => [
            'template' => 'Leafiny_Commerce::block/checkout/review/agreements.twig',
            'class'    => Commerce_Block_Checkout_Review_Agreements::class
        ],

        'commerce.complete' => [
            'template' => 'Leafiny_Commerce::block/complete.twig',
            'class'    => Commerce_Block_Complete::class
        ],

        'admin.head' => [
            'stylesheet' => [
                'Leafiny_Commerce::backend/css/commerce.css' => 400,
            ],
        ],
        'admin.commerce.product.form.additional' => [
            'template' => 'Leafiny_Commerce::block/backend/product/form/additional.twig',
            'context'  => Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND,
            'class'    => Commerce_Block_Backend_Product_Form_Additional::class,
        ],
        'admin.menu' => [
            'tree' => [
                800 => [
                    'Commerce' => [
                        10 => [
                            'title' => 'Orders',
                            'path'  => '/admin/*/orders/list/'
                        ],
                        20 => [
                            'title' => 'Cart Rules',
                            'path'  => '/admin/*/cart-rules/list/'
                        ],
                        30 => [
                            'title' => 'Taxes',
                            'path'  => '/admin/*/taxes/list/'
                        ],
                        40 => [
                            'title' => 'Shipping',
                            'path'  => '/admin/*/shipping/list/'
                        ],
                        50 => [
                            'title' => 'Statuses',
                            'path'  => '/admin/*/status/list/'
                        ],
                    ]
                ]
            ]
        ],
    ],

    'mail' => [
        'order' => [
            'subject'  => 'Your order has been registered',
            'template' => 'Leafiny_Commerce::mail/order.twig',
            'class'    => Commerce_Mail_Order::class
        ],
        'history' => [
            'subject'  => 'Your order has been updated',
            'template' => 'Leafiny_Commerce::mail/history.twig',
        ]
    ],

    'events' => [
        'catalog_product_get_list_after' => [
            'set_product_list_final_price' => 100,
        ],
        'catalog_product_get_after' => [
            'set_product_final_price' => 100,
        ],
        'sale_save_after' => [
            'init_sale_address' => 100,
        ],
        'sale_init_after' => [
            'init_sale_shipping'   => 100,
        ],
        'commerce_order_place' => [
            'process_payment' => 100,
        ],
        'order_complete_after' => [
            'update_cart_rule_coupon' => 100,
        ],

        'checkout_action_step_addresses' => [
            'checkout_addresses_view' => 100,
        ],
        'checkout_action_step_shipping' => [
            'checkout_shipping_view' => 100,
        ],
        'checkout_action_step_payment' => [
            'checkout_payment_view' => 100,
        ],
        'checkout_action_step_review' => [
            'checkout_review_view' => 100,
        ],

        'checkout_action_validate_addresses' => [
            'checkout_addresses_validate' => 100,
        ],
        'checkout_action_validate_shipping' => [
            'checkout_shipping_validate' => 100,
        ],
        'checkout_action_validate_payment' => [
            'checkout_payment_validate' => 100,
        ],
        'checkout_action_validate_review' => [
            'checkout_review_validate' => 100,
        ],

        'checkout_action_save_addresses' => [
            'checkout_addresses_save' => 100,
        ],
        'checkout_action_save_shipping' => [
            'checkout_shipping_save' => 100,
        ],
        'checkout_action_save_payment' => [
            'checkout_payment_save' => 100,
        ],
        'checkout_action_save_review' => [
            'checkout_review_save' => 100,
        ],
    ],

    'observer' => [
        'set_product_list_final_price' => [
            'class' => Commerce_Observer_Product_List_FinalPrice::class
        ],
        'set_product_final_price' => [
            'class' => Commerce_Observer_Product_FinalPrice::class
        ],
        'init_sale_address' => [
            'class' => Commerce_Observer_Sale_InitAddress::class
        ],
        'init_sale_shipping' => [
            'class' => Commerce_Observer_Sale_InitShipping::class
        ],
        'process_payment' => [
            'class' => Commerce_Observer_Sale_Payment::class
        ],
        'update_cart_rule_coupon' => [
            'class' => Commerce_Observer_Sale_UpdateCoupon::class
        ],

        'checkout_addresses_view' => [
            'class' => Commerce_Observer_Checkout_Addresses_View::class
        ],
        'checkout_addresses_save' => [
            'class' => Commerce_Observer_Checkout_Addresses_Save::class
        ],
        'checkout_addresses_validate' => [
            'class' => Commerce_Observer_Checkout_Addresses_Validate::class
        ],

        'checkout_shipping_view' => [
            'class' => Commerce_Observer_Checkout_Shipping_View::class
        ],
        'checkout_shipping_save' => [
            'class' => Commerce_Observer_Checkout_Shipping_Save::class
        ],
        'checkout_shipping_validate' => [
            'class' => Commerce_Observer_Checkout_Shipping_Validate::class
        ],

        'checkout_payment_view' => [
            'class' => Commerce_Observer_Checkout_Payment_View::class
        ],
        'checkout_payment_save' => [
            'class' => Commerce_Observer_Checkout_Payment_Save::class
        ],
        'checkout_payment_validate' => [
            'class' => Commerce_Observer_Checkout_Payment_Validate::class
        ],

        'checkout_review_view' => [
            'class' => Commerce_Observer_Checkout_Review_View::class
        ],
        'checkout_review_save' => [
            'class' => Commerce_Observer_Checkout_Review_Save::class
        ],
        'checkout_review_validate' => [
            'class' => Commerce_Observer_Checkout_Review_Validate::class
        ],
    ]
];