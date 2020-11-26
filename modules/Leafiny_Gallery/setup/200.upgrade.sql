CREATE TABLE IF NOT EXISTS `gallery_image` (
    `image_id`    INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `entity_id`   INT(11) unsigned NOT NULL,
    `entity_type` VARCHAR(255) NOT NULL,
    `image`       VARCHAR(255) NOT NULL,
    `label`       VARCHAR(255) NULL,
    `position`    INT(10) NOT NULL DEFAULT 1,
    `status`      INT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;