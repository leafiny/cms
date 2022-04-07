CREATE TABLE IF NOT EXISTS `commerce_sale` (
    `sale_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `sale_increment_id` VARCHAR(255) NULL,
    `sale_currency` CHAR(3) NULL,
    `sale_comment` TEXT,
    `incl_tax_subtotal` DECIMAL(10,2) NULL,
    `incl_tax_shipping` DECIMAL(10,2) NULL,
    `incl_tax_discount` DECIMAL(10,2) NULL,
    `incl_tax_subtotal_with_discount` DECIMAL(10,2) NULL,
    `excl_tax_subtotal` DECIMAL(10,2) NULL,
    `excl_tax_shipping` DECIMAL(10,2) NULL,
    `excl_tax_discount` DECIMAL(10,2) NULL,
    `excl_tax_subtotal_with_discount` DECIMAL(10,2) NULL,
    `tax_subtotal` DECIMAL(10,2) NULL,
    `tax_shipping` DECIMAL(10,2) NULL,
    `tax_discount` DECIMAL(10,2) NULL,
    `incl_tax_total` DECIMAL(10,2) NULL,
    `excl_tax_total` DECIMAL(10,2) NULL,
    `tax_total` DECIMAL(10,2) NULL,
    `email` VARCHAR(255) NULL,
    `customer` VARCHAR(255) NULL,
    `state` VARCHAR(255) DEFAULT 'cart',
    `status` VARCHAR(255) NULL,
    `shipping_method` VARCHAR(255) NULL,
    `total_weight` DECIMAL(10,2) NULL,
    `total_qty` INT(10) NULL,
    `payment_method` VARCHAR(255) NULL,
    `payment_title` VARCHAR(255) NULL,
    `payment_ref` VARCHAR(255) NULL,
    `payment_state` VARCHAR(255) NULL,
    `payment_data` TEXT,
    `invoice_increment_id` VARCHAR(255) NULL,
    `agreements` INT(1) NOT NULL DEFAULT 0,
    `language` VARCHAR(5) NOT NULL DEFAULT 'en_US',
    `key` VARCHAR(255) NOT NULL,
    `rule_ids` VARCHAR(255) NULL,
    `coupon_code` VARCHAR(255) NULL,
    `coupon_id` INT(10) NULL,
    `coupon_rule_id` INT(10) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_sale_address` (
    `address_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `sale_id` INT(10) unsigned NOT NULL,
    `type` VARCHAR(255) NULL,
    `company` VARCHAR(255) NOT NULL DEFAULT '',
    `firstname` VARCHAR(255) NOT NULL DEFAULT '',
    `lastname` VARCHAR(255) NOT NULL DEFAULT '',
    `street_1` VARCHAR(255) NOT NULL DEFAULT '',
    `street_2` VARCHAR(255) NOT NULL DEFAULT '',
    `postcode` VARCHAR(255) NOT NULL DEFAULT '',
    `telephone` VARCHAR(255) NOT NULL DEFAULT '',
    `state_code` VARCHAR(255) NOT NULL DEFAULT '',
    `city` VARCHAR(255) NOT NULL DEFAULT '',
    `country_code` VARCHAR(2) NOT NULL DEFAULT '',
    `same_as_shipping` INT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`address_id`),
    UNIQUE KEY (`sale_id`, `type`),
    FOREIGN KEY (`sale_id`) REFERENCES `commerce_sale` (`sale_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_sale_item` (
    `item_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `product_id` INT(10) unsigned NOT NULL,
    `product_sku` VARCHAR(255) NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `product_path` VARCHAR(255) NULL,
    `sale_id` INT(10) unsigned NOT NULL,
    `qty` INT(10) NOT NULL,
    `max_qty` INT(10) NOT NULL,
    `incl_tax_unit` DECIMAL(10,2) NULL,
    `excl_tax_unit` DECIMAL(10,2) NULL,
    `incl_tax_row` DECIMAL(10,2) NULL,
    `excl_tax_row` DECIMAL(10,2) NULL,
    `weight_unit` DECIMAL(10,2) NULL,
    `weight_row` DECIMAL(10,2) NULL,
    `tax_rule_id` INT(10) unsigned NULL,
    `tax_percent` DECIMAL(5,2) NULL,
    `tax_amount_unit` DECIMAL(10,2) NULL,
    `tax_amount_row` DECIMAL(10,2) NULL,
    `custom_incl_tax_unit` DECIMAL(10,2) NULL,
    `custom_excl_tax_unit` DECIMAL(10,2) NULL,
    `discount_excl_tax_unit` DECIMAL(10,2) NULL,
    `discount_incl_tax_unit` DECIMAL(10,2) NULL,
    `discount_excl_tax_row` DECIMAL(10,2) NULL,
    `discount_incl_tax_row` DECIMAL(10,2) NULL,
    `discount_tax_unit` DECIMAL(10,2) NULL,
    `discount_tax_row` DECIMAL(10,2) NULL,
    `options` TEXT NULL,
    PRIMARY KEY (`item_id`),
    FOREIGN KEY (`sale_id`) REFERENCES `commerce_sale` (`sale_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_sale_history` (
    `history_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `sale_id` INT(10) unsigned NOT NULL,
    `status_code` VARCHAR(255) NULL,
    `title` VARCHAR(255) NULL,
    `comment` TEXT NULL,
    `operator` VARCHAR(255) NULL DEFAULT 'System',
    `mail_sent` INT(1) unsigned NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`history_id`),
    FOREIGN KEY (`sale_id`) REFERENCES `commerce_sale` (`sale_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_sale_status` (
    `status_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(255) NULL,
    `label` VARCHAR(255) NULL,
    `language` VARCHAR(5) NOT NULL DEFAULT 'en_US',
    `comment` TEXT NULL,
    PRIMARY KEY (`status_id`),
    UNIQUE KEY (`code`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `commerce_sale_status` (`code`, `label`, `comment`) VALUES
('pending_payment', 'Pending Payment', 'Your order has been registered, payment is pending'),
('pending', 'Pending', 'Your order is pending'),
('processing', 'Processing', 'Your order is being prepared'),
('shipped', 'Shipped', 'The package has been shipped'),
('canceled', 'Canceled', 'The order has been canceled'),
('refunded', 'Refunded', 'The order has been refunded');

CREATE TABLE IF NOT EXISTS `commerce_tax_rule` (
    `rule_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(255) NULL,
    PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_tax` (
    `tax_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `rule_id` INT(10) unsigned NULL,
    `label` VARCHAR(255) NULL,
    `tax_percent` DECIMAL(5,2) NULL,
    `country_code` VARCHAR(2) NULL,
    `state_code` VARCHAR(10) NULL,
    `postcode` VARCHAR(32) NULL,
    FOREIGN KEY (`rule_id`) REFERENCES `commerce_tax_rule` (`rule_id`) ON UPDATE CASCADE ON DELETE SET NULL,
    PRIMARY KEY (`tax_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `catalog_product` ADD COLUMN `special_price` DECIMAL(10,2) NULL;
ALTER TABLE `catalog_product` ADD COLUMN `options` TEXT NULL;
ALTER TABLE `catalog_product` ADD COLUMN `tax_rule_id` INT(10) unsigned NULL;
ALTER TABLE `catalog_product` ADD COLUMN `price_type` VARCHAR(8) NULL;
ALTER TABLE `catalog_product` ADD COLUMN `qty` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `catalog_product` ADD FOREIGN KEY (`tax_rule_id`) REFERENCES `commerce_tax_rule` (`rule_id`) ON UPDATE CASCADE ON DELETE SET NULL;

INSERT INTO `commerce_tax_rule` (label) VALUES ('Default');

CREATE TABLE IF NOT EXISTS `commerce_shipping` (
    `shipping_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(255) NULL,
    `method` VARCHAR(255) NULL,
    `tax_rule_id` INT(10) unsigned NULL,
    `price_type` VARCHAR(8) NULL,
    `countries` TEXT NULL,
    `states` TEXT NULL,
    `postcodes` TEXT NULL,
    `price_lines` INT(10) NOT NULL DEFAULT 1,
    `priority` INT(10) NOT NULL DEFAULT 0,
    `tracking_url` VARCHAR(255) NULL,
    FOREIGN KEY (`tax_rule_id`) REFERENCES `commerce_tax_rule` (`rule_id`) ON UPDATE CASCADE ON DELETE SET NULL,
    UNIQUE KEY (`method`),
    PRIMARY KEY (`shipping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_shipping_price` (
    `shipping_id` INT(10) unsigned NOT NULL,
    `weight_from` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (`shipping_id`) REFERENCES `commerce_shipping` (`shipping_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_sale_shipment` (
    `shipment_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `sale_id` INT(10) unsigned NOT NULL,
    `shipping_method` VARCHAR(255) NULL,
    `tracking_number` VARCHAR(255) NULL,
    `tracking_url` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`shipment_id`),
    FOREIGN KEY (`sale_id`) REFERENCES `commerce_sale` (`sale_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_cart_rule` (
    `rule_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `status` INT(1) NOT NULL DEFAULT 0,
    `conditions` TEXT NULL,
    `discount` DECIMAL(5,2) NULL DEFAULT 0,
    `option` VARCHAR(255) NULL,
    `type` VARCHAR(255) NOT NULL,
    `expire` DATETIME NULL,
    `priority` INT(11) NOT NULL DEFAULT 0,
    `stop_rules_processing` INT(1) NOT NULL DEFAULT 0,
    `has_coupon` INT(1) NOT NULL DEFAULT 0,
    `coupon_number` INT(11) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `commerce_cart_rule_coupon` (
    `coupon_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `rule_id` INT(10) unsigned NOT NULL,
    `code` VARCHAR(255) NOT NULL,
    `status` INT(1) NOT NULL DEFAULT 1,
    `used` INT(10) unsigned NOT NULL DEFAULT 0,
    `limit` INT(10) unsigned NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`coupon_id`),
    UNIQUE KEY (`code`),
    FOREIGN KEY (`rule_id`) REFERENCES `commerce_cart_rule` (`rule_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;