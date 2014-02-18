RENAME TABLE `wp_users_purchases` TO `wp_users_purchases_backup`;

CREATE TABLE `wp_users_purchases` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT '0',
  `paid_amount` int(11) DEFAULT '0',
  `card_id` int(11) DEFAULT '0',
  `created_date` datetime DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' COMMENT 'Active,InArrears,Frozen,Unsubscribing',
  `inarrears_count` int(11) DEFAULT '0',
  `frozen_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `wp_users_subscriptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `suite_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `subscribed_date` datetime DEFAULT NULL,
  `esb_user_id` int(11) DEFAULT NULL,
  `harness_username` varchar(255) DEFAULT NULL,
  `harness_password` varchar(255) DEFAULT NULL,
  `harness_endpoint_url` varchar(500) DEFAULT '',
  `p_mode_agreement` enum('LIGHT','HIGH-END') DEFAULT 'LIGHT',
  `tester_username` varchar(255) DEFAULT NULL,
  `tester_password` varchar(255) DEFAULT NULL,
  `tester_endpoint_url` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' COMMENT 'Active,InArrears,Frozen,Unsubscribing',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

