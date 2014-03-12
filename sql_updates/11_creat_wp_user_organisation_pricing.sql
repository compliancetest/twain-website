CREATE TABLE `wp_users_organisation_pricing` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `family_mark` int(11) DEFAULT '0',
  `user_count` int(11) DEFAULT '0',
  `joined_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `wp_users_organisation_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) DEFAULT NULL,
  `family_mark` int(11) DEFAULT NULL,
  `payer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
