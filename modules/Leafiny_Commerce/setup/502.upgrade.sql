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

ALTER TABLE `commerce_shipping` ADD COLUMN `tracking_url` VARCHAR(255) NULL;