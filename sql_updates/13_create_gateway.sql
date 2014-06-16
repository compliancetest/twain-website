DROP TABLE IF EXISTS `wp_gateways`;
CREATE TABLE `wp_gateways` (
  `gateway_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `abn` varchar(11) NOT NULL DEFAULT '',
  `contribution_test_url` varchar(200) NOT NULL DEFAULT '',
  `contribution_prod_url` varchar(200) NOT NULL DEFAULT '',
  `rollover_test_url` varchar(200) NOT NULL DEFAULT '',
  `rollover_prod_url` varchar(200) NOT NULL DEFAULT '',
  `certificate` longtext,
  `certificate_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`gateway_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;