CREATE TABLE IF NOT EXISTS `wp_login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attempts` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL,
  `last_attempts_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;