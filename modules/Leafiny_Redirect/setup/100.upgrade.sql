CREATE TABLE IF NOT EXISTS `url_redirect` (
 `redirect_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `source_identifier` varchar(255) NOT NULL,
 `target_identifier` varchar(255) NOT NULL,
 `redirect_type` INT(3) NOT NULL DEFAULT 301,
 `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`redirect_id`),
 UNIQUE KEY (`source_identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
