CREATE TABLE IF NOT EXISTS `search_fulltext` (
 `search_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `content` TEXT NOT NULL,
 `object_type` varchar(32) NULL DEFAULT NULL,
 `object_id` int(10) unsigned NOT NULL,
 `language` VARCHAR(5) NULL,
 PRIMARY KEY (`search_id`),
 UNIQUE (`object_type`, `object_id`),
 FULLTEXT (`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `search_words` (
 `word` varchar(255) NOT NULL,
 `language` VARCHAR(5) NULL,
 UNIQUE (`word`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
