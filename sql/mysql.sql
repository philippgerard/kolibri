CREATE TABLE `pages` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`   VARCHAR(256) DEFAULT NULL,
  `content` TEXT,
  PRIMARY KEY (`id`),
  KEY `title` (`title`(255))
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE `versions` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id` INT(11) DEFAULT NULL,
  `version` INT(11) DEFAULT NULL,
  `content` TEXT,
  `created` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `version` (`version`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

INSERT INTO `pages` (`id`, `title`, `content`)
  VALUES
  (1, 'Index', 'Welcome to Kolibri.');

INSERT INTO `versions` (`id`, `page_id`, `version`, `content`, `created`)
  VALUES
  (1, 1, 1, 'Welcome to Kolibri.', NOW());
