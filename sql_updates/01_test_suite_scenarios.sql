CREATE TABLE `wp_test_suites_scenarios` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `suite_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `sequence` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
