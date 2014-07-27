CREATE TABLE `wp_organisations_subscriptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(200) DEFAULT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `purchaser_id` int(11) DEFAULT NULL,
  `purchased_date` datetime DEFAULT NULL,
  `status` enum('Active','InArrears','Frozen','Unsubscribing') DEFAULT NULL,
  `suite_family_mark` int(11) DEFAULT NULL,
  `payment_method` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `wp_users_subscriptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `suite_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `harness_username` varchar(30) DEFAULT NULL,
  `harness_password` varchar(255) DEFAULT NULL,
  `harness_endpoint_url` varchar(500) DEFAULT '',
  `harness_key` blob,
  `harness_certificate` blob,
  `harness_certificate_p12` blob,
  `p_mode_agreement` enum('LIGHT','HIGH-END') DEFAULT 'LIGHT',
  `tester_username` varchar(30) DEFAULT NULL,
  `tester_password` varchar(255) DEFAULT NULL,
  `tester_endpoint_url` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' COMMENT 'Active,InArrears,Frozen,Unsubscribing',
  `profile_id` int(11) DEFAULT NULL,
  `entity_id` varchar(45) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `gateway_id` int(11) DEFAULT NULL,
  `alias` varchar(30) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

