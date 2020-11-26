CREATE TABLE IF NOT EXISTS `category` (
    `category_id`      INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `parent_id`        INT(11) NULL DEFAULT NULL,
    `path_key`         VARCHAR(255) NOT NULL,
    `name`             VARCHAR(255) NULL,
    `position`         INT(10) NOT NULL DEFAULT 0,
    `content`          TEXT NULL,
    `status`           INT(1) NOT NULL DEFAULT 0,
    `show_in_menu`     INT(1) NOT NULL DEFAULT 1,
    `language`         VARCHAR(5) NOT NULL DEFAULT 'en_US',
    `meta_title`       VARCHAR(255) NULL DEFAULT '',
    `meta_description` VARCHAR(255) NULL DEFAULT '',
    `canonical`        VARCHAR(255) NULL DEFAULT NULL,
    `robots`           VARCHAR(255) NULL DEFAULT NULL,
    `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`category_id`),
    UNIQUE KEY (`path_key`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;