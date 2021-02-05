CREATE TABLE IF NOT EXISTS `gallery_group` (
    `group_id`   INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `path_key`   VARCHAR(255) NOT NULL,
    `language`   VARCHAR(5) NOT NULL DEFAULT 'en_US',
    `name`       VARCHAR(255) NOT NULL,
    `type`       VARCHAR(255) NOT NULL,
    `status`     INT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `gallery_image` ADD COLUMN `link` VARCHAR(255) NULL AFTER `label`;
ALTER TABLE `gallery_image` ADD COLUMN `text` TEXT NULL AFTER `link`;