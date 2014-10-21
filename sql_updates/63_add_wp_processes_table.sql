CREATE TABLE IF NOT EXISTS `wp_processes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `version_major` int(11) NOT NULL,
  `version_minor` int(11) NOT NULL,
  `patch_version` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO `wp_processes` (`id`, `title`, `name`, `version_major`, `version_minor`, `patch_version`) VALUES
(1, 'Contributions', 'SSP-CONT', 1, 2, 0);