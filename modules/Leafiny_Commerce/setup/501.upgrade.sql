CREATE TABLE IF NOT EXISTS `commerce_cart_rule` (
   `rule_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` VARCHAR(255) NOT NULL,
   `description` TEXT NULL,
   `status` INT(1) NOT NULL DEFAULT 0,
   `discount` DECIMAL(5,2) NULL DEFAULT 0,
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

ALTER TABLE `commerce_sale` DROP COLUMN `discount_percent`;
ALTER TABLE `commerce_sale` DROP COLUMN `tax_percent`;
ALTER TABLE `commerce_sale` ADD COLUMN `rule_ids` VARCHAR(255) NULL;
ALTER TABLE `commerce_sale` ADD COLUMN `coupon_code` VARCHAR(255) NULL;
ALTER TABLE `commerce_sale` ADD COLUMN `coupon_id` INT(10) NULL;
ALTER TABLE `commerce_sale` ADD COLUMN `coupon_rule_id` INT(10) NULL;

ALTER TABLE `commerce_sale_item` ADD COLUMN `discount_excl_tax_unit` DECIMAL(10,2) NULL;
ALTER TABLE `commerce_sale_item` ADD COLUMN `discount_incl_tax_unit` DECIMAL(10,2) NULL;
ALTER TABLE `commerce_sale_item` ADD COLUMN `discount_excl_tax_row` DECIMAL(10,2) NULL;
ALTER TABLE `commerce_sale_item` ADD COLUMN `discount_incl_tax_row` DECIMAL(10,2) NULL;
ALTER TABLE `commerce_sale_item` ADD COLUMN `discount_tax_unit` DECIMAL(10,2) NULL;
ALTER TABLE `commerce_sale_item` ADD COLUMN `discount_tax_row` DECIMAL(10,2) NULL;