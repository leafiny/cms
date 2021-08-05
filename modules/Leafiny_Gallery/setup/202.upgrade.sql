CREATE TABLE IF NOT EXISTS `gallery_group_category` (
    `category_id` INT(11) unsigned NOT NULL,
    `group_id`  INT(11) unsigned NOT NULL,
    FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`group_id`) REFERENCES `gallery_group` (`group_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY (`category_id`, `group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;