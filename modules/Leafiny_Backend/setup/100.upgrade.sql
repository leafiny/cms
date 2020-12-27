CREATE TABLE IF NOT EXISTS `admin_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(32) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_admin` INT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `admin_user` (`username`, `password`) VALUES ('admin', '$2y$10$wgRJ.W9oVeDQ7YCPJoUdj.tyAZ8knIphkmtG21Uko0tDRdM0vSxzi');

CREATE TABLE IF NOT EXISTS `admin_user_resources` (
  `resource_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `resource` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`resource_id`),
  FOREIGN KEY (`user_id`) REFERENCES `admin_user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  UNIQUE KEY (`user_id`, `resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
