CREATE TABLE IF NOT EXISTS `blog_post_comment` (
  `post_id`    INT(11) unsigned NOT NULL,
  `comment_id` INT(11) unsigned NOT NULL,
  FOREIGN KEY (`post_id`) REFERENCES `blog_post` (`post_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`comment_id`) REFERENCES `social_comment` (`comment_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  UNIQUE KEY (`post_id`, `comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;