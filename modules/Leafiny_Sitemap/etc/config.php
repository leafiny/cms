<?php

$config = [
    'model' => [
        'sitemap' => [
            'class'  => Sitemap_Model_Sitemap::class,
            'entity' => [
                'catalog_product' => [
                    'enabled' => true,
                ],
                'category' => [
                    'enabled' => true,
                ],
                'blog_post' => [
                    'enabled' => true,
                ],
                'cms_page' => [
                    'enabled' => true,
                ],
            ],
        ],
    ],

    'page' => [
        '/sitemap.xml' => [
            'class'    => Sitemap_Page_Sitemap::class,
            'template' => 'Leafiny_Sitemap::sitemap.xml.twig',
            'content'  => 'Leafiny_Sitemap::page/urls.xml.twig',
        ],
        '/sitemap.*.xml' => [
            'class'    => Sitemap_Page_Sitemap::class,
            'template' => 'Leafiny_Sitemap::sitemap.xml.twig',
            'content'  => 'Leafiny_Sitemap::page/urls.xml.twig',
        ],
    ]
];
