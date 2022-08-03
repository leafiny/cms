ALTER TABLE `social_comment` ADD COLUMN `link` VARCHAR(255) NULL DEFAULT NULL AFTER `referer`;
ALTER TABLE `social_comment` ADD COLUMN `entity_id` INT(11) unsigned NOT NULL;
ALTER TABLE `social_comment` ADD COLUMN `entity_type` VARCHAR(255) NOT NULL;