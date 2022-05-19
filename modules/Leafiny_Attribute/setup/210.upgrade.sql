CREATE TABLE IF NOT EXISTS `attribute` (
     `attribute_id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
     `label` VARCHAR(255) NOT NULL,
     `code` VARCHAR(255) NOT NULL,
     `entity_type` VARCHAR(255) NOT NULL,
     `input_type` VARCHAR(255) NOT NULL DEFAULT 'text',
     `option_qty` INT(10) NOT NULL DEFAULT 0,
     `position` INT(10) NOT NULL DEFAULT 0,
     PRIMARY KEY (`attribute_id`),
     UNIQUE KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `attribute_option` (
    `option_id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `attribute_id` INT(11) unsigned NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `custom` VARCHAR(255) NULL,
    `position` INT(10) NOT NULL DEFAULT 0,
    PRIMARY KEY (`option_id`),
    FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`attribute_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `attribute_translate` (
     `attribute_id` INT(11) unsigned NOT NULL,
     `label` VARCHAR(255) NOT NULL,
     `language` VARCHAR(5) NOT NULL DEFAULT 'en_US',
     FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`attribute_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `attribute_option_translate` (
     `option_id` INT(11) unsigned NOT NULL,
     `label` VARCHAR(255) NOT NULL,
     `language` VARCHAR(5) NOT NULL DEFAULT 'en_US',
     FOREIGN KEY (`option_id`) REFERENCES `attribute_option` (`option_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `attribute_entity_value` (
    `attribute_id` INT(11) unsigned NOT NULL,
    `attribute_code` VARCHAR(255) NOT NULL,
    `option_id` INT(11) NOT NULL default 0,
    `option_custom` VARCHAR(255) NULL,
    `entity_type` VARCHAR(255) NOT NULL,
    `entity_id` INT(11) unsigned NOT NULL,
    `language` VARCHAR(5) NOT NULL DEFAULT 'en_US',
    `value` VARCHAR(255),
    FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`attribute_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY (`attribute_id`, `option_id`, `entity_type`, `entity_id`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;