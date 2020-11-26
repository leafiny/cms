CREATE TABLE IF NOT EXISTS `catalog_product` (
    `product_id`       INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `path_key`         VARCHAR(255) NOT NULL,
    `sku`              VARCHAR(255) NOT NULL,
    `weight`           DECIMAL(12,2) NULL,
    `name`             VARCHAR(255) NULL,
    `price`            DECIMAL(12,2) NULL,
    `description`      TEXT NULL,
    `status`           INT(1) NOT NULL DEFAULT 0,
    `language`         VARCHAR(5) NOT NULL DEFAULT 'en_US',
    `meta_title`       VARCHAR(255) NULL DEFAULT '',
    `meta_description` VARCHAR(255) NULL DEFAULT '',
    `canonical`        VARCHAR(255) NULL DEFAULT NULL,
    `robots`           VARCHAR(255) NULL DEFAULT NULL,
    `breadcrumb`       INT(11) NULL DEFAULT NULL,
    `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`product_id`),
    UNIQUE KEY (`path_key`, `language`),
    UNIQUE KEY (`sku`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `catalog_product_category` (
    `category_id` INT(11) unsigned NOT NULL,
    `product_id`  INT(11) unsigned NOT NULL,
    FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY (`category_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;