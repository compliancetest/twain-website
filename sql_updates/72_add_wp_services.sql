CREATE TABLE IF NOT EXISTS `wp_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wp_post_id` int(11) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_identifier` varchar(255) NOT NULL,
  `owner_type` varchar(255) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_description` text NOT NULL,
  `service_visibility` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `test_suite_id` int(11) NOT NULL,
  `roles` varchar(255) NOT NULL,
  `levels` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `wp_services` ADD `organisation_id` INT NOT NULL AFTER `levels` ;