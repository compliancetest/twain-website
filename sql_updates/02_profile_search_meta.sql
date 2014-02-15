DROP TABLE IF EXISTS `wp_community_profile_meta`;

CREATE TABLE `wp_community_profile_meta` (
  `meta_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `meta_key` varchar(100) CHARACTER SET latin1 NOT NULL,
  `meta_value` varchar(100) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`meta_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;