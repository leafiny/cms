CREATE TABLE IF NOT EXISTS `blog_post` (
    `post_id`          INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `path_key`         VARCHAR(255) NOT NULL,
    `title`            VARCHAR(255) NULL DEFAULT '',
    `meta_title`       VARCHAR(255) NULL DEFAULT '',
    `meta_description` VARCHAR(255) NULL DEFAULT '',
    `canonical`        VARCHAR(255) NULL DEFAULT NULL,
    `robots`           VARCHAR(255) NULL DEFAULT NULL,
    `intro`            TEXT NULL,
    `content`          TEXT NULL,
    `language`         VARCHAR(5) DEFAULT 'en_US',
    `status`           INT(1) NOT NULL DEFAULT 0,
    `breadcrumb`       INT(11) NULL DEFAULT NULL,
    `author`           VARCHAR(255) NULL DEFAULT NULL,
    `publish_date`     DATE NULL DEFAULT NULL,
    `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`post_id`),
    UNIQUE KEY (`path_key`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `blog_post_category` (
    `category_id` INT(11) unsigned NOT NULL,
    `post_id`  INT(11) unsigned NOT NULL,
    FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`post_id`) REFERENCES `blog_post` (`post_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY (`category_id`, `post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
