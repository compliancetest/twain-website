CREATE TABLE `wp_organisations_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
