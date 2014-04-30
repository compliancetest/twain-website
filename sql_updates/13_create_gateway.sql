DROP TABLE IF EXISTS `wp_gateways`;
CREATE TABLE `wp_gateways` (
  `gateway_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `abn` varchar(11) NOT NULL,
  `url` varchar(200) NOT NULL,
  PRIMARY KEY (`gateway_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;