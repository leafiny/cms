CREATE TABLE IF NOT EXISTS `social_comment` (
    `comment_id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(255) NULL,
    `name`       VARCHAR(255) NULL,
    `comment`    TEXT NULL,
    `note`       INT(10) NULL,
    `status`     INT(1) NOT NULL DEFAULT 0,
    `language`   VARCHAR(5) NOT NULL DEFAULT 'en_US',
    `ip_address` VARCHAR(255) NULL,
    `referer`    VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;