CREATE TABLE IF NOT EXISTS `contact_message` (
    `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NULL,
    `email` varchar(255) NULL,
    `message` TEXT,
    `is_open` INT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;