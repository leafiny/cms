CREATE TABLE IF NOT EXISTS `url_rewrite` (
 `rewrite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `source_identifier` varchar(255) NOT NULL,
 `target_identifier` varchar(255) NOT NULL,
 `object_type` varchar(32) NULL DEFAULT NULL,
 `is_system` INT(1) NOT NULL DEFAULT 0,
 PRIMARY KEY (`rewrite_id`),
 UNIQUE KEY (`source_identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
