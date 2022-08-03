ALTER TABLE `catalog_product` ADD COLUMN `allow_backorders` INT(1) NOT NULL DEFAULT 0;
ALTER TABLE `catalog_product` ADD COLUMN `max_allowed_quantity` INT(10) unsigned NULL DEFAULT NULL;