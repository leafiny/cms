CREATE TABLE IF NOT EXISTS `log_message` (
    `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `message` TEXT,
    `level` int(3) NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;