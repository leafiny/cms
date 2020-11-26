CREATE TABLE IF NOT EXISTS `cms_block` (
    `block_id`   INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `path_key`   VARCHAR(255) NOT NULL,
    `content`    TEXT NULL,
    `language`   VARCHAR(5) NOT NULL DEFAULT 'en_US',
    `status`     INT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`block_id`),
    UNIQUE KEY (`path_key`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cms_block_category` (
    `category_id` INT(11) unsigned NOT NULL,
    `block_id`  INT(11) unsigned NOT NULL,
    FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`block_id`) REFERENCES `cms_block` (`block_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY (`category_id`, `block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cms_page` (
    `page_id`          INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `path_key`         VARCHAR(255) NOT NULL,
    `title`            VARCHAR(255) NULL DEFAULT '',
    `meta_title`       VARCHAR(255) NULL DEFAULT '',
    `meta_description` VARCHAR(255) NULL DEFAULT '',
    `canonical`        VARCHAR(255) NULL DEFAULT NULL,
    `robots`           VARCHAR(255) NULL DEFAULT NULL,
    `content`          TEXT NULL,
    `language`         VARCHAR(5) DEFAULT 'en_US',
    `status`           INT(1) NOT NULL DEFAULT 0,
    `breadcrumb`       INT(11) NULL DEFAULT NULL,
    `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`page_id`),
    UNIQUE KEY (`path_key`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cms_page_category` (
    `category_id` INT(11) unsigned NOT NULL,
    `page_id`  INT(11) unsigned NOT NULL,
    FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`page_id`) REFERENCES `cms_page` (`page_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY (`category_id`, `page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cms_page` (`path_key`, `title`, `meta_title`, `content`, `canonical`, `status`, `language`) VALUES
('privacy-policy', 'Privacy Policy', 'Privacy Policy', '<p>When you use our services, you\'re trusting us with your information.</p>', '/privacy-policy.html', 1, 'en_US'),
('confidentialite', 'Politique de confidentialité', 'Politique de confidentialité', '<p>Lorsque vous utilisez nos services, vous nous confiez vos informations.</p>', '/confidentialite.html', 1, 'fr_FR');

INSERT INTO `url_rewrite` (`source_identifier`, `target_identifier`, `object_type`, `is_system`) VALUES
('/privacy-policy.html', '/page/privacy-policy.html', 'cms_page', 1),
('/confidentialite.html', '/page/confidentialite.html', 'cms_page', 1);

INSERT INTO `cms_block` (`path_key`, `content`, `status`, `language`) VALUES
('welcome', '<p>Welcome to <strong>Leafiny</strong>. This is the home page. Edit it and start your new amazing website.</p>', 1, 'en_US'),
('welcome', '<p>Bienvenue sur <strong>Leafiny</strong>. Ceci est la page d\'accueil. Modifiez là et commencez à construire votre site.</p>', 1, 'fr_FR'),
('copyright', CONCAT('<p>Leafiny © ', YEAR(CURDATE()), '</p>'), 1, 'en_US'),
('copyright', CONCAT('<p>Leafiny © ', YEAR(CURDATE()), '</p>'), 1, 'fr_FR'),
('footer-links', '<ul><li><a href="{{ page.getBaseUrl }}">Home</a></li><li><a href="{{ page.getBaseUrl }}privacy-policy.html">Privacy Policy</a></li><li><a href="{{ page.getBaseUrl }}contact.html">Contact</a><br></li></ul>', 1, 'en_US'),
('footer-links', '<ul><li><a href="{{ page.getBaseUrl }}">Accueil</a></li><li><a href="{{ page.getBaseUrl }}confidentialite.html">Politique de confidentialité</a></li><li><a href="{{ page.getBaseUrl }}contact.html">Contact</a><br></li></ul>', 1, 'fr_FR');
