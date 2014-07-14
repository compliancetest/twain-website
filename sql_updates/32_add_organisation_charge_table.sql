CREATE TABLE `wp_organisations_charge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `reference_type` enum('ticket','subscription') DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `invoice_identifier` varchar(255) DEFAULT NULL,
  `is_paid` tinyint(2) DEFAULT '0',
  `comment` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
