<?php

$config = [
    'model' => [
        Payment_Model_Payment_Offline_BankTransfer::PAYMENT_METHOD => [
            'class'       => Payment_Model_Payment_Offline_BankTransfer::class,
            'title'       => 'Bank Transfer',
            'description' => 'Bank Transfer Payment',
            'is_enabled'  => true,
        ],
        Payment_Model_Payment_Offline_BankCheck::PAYMENT_METHOD => [
            'class'       => Payment_Model_Payment_Offline_BankCheck::class,
            'title'       => 'Bank Check',
            'description' => 'Bank Check Payment',
            'is_enabled'  => true,
        ],
    ],

    'helper' => [
        'payment' => [
            'payment_methods' => [
                Payment_Model_Payment_Offline_BankTransfer::PAYMENT_METHOD => 200,
                Payment_Model_Payment_Offline_BankCheck::PAYMENT_METHOD => 300,
            ],
        ],
    ],

    'block' => [
        Payment_Model_Payment_Offline_BankTransfer::PAYMENT_METHOD . '.payment.info' => [
            'template' => 'Leafiny_Payment::block/banktransfer/info.twig',
            'iban'     => 'FR7630001007941234567890185',
            'bic'      => 'BDFEFRPPXXX',
        ],
        Payment_Model_Payment_Offline_BankTransfer::PAYMENT_METHOD . '.payment.complete' => [
            'template' => 'Leafiny_Payment::block/complete.twig',
        ],

        Payment_Model_Payment_Offline_BankCheck::PAYMENT_METHOD . '.payment.info' => [
            'template' => 'Leafiny_Payment::block/bankcheck/info.twig',
            'address'  => 'Company - 1210 Rosewood Ave - Austin - 78702 Texas',
        ],
        Payment_Model_Payment_Offline_BankCheck::PAYMENT_METHOD . '.payment.complete' => [
            'template' => 'Leafiny_Payment::block/complete.twig',
        ],
    ],

];
