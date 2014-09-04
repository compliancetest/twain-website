CREATE TABLE `wp_users_payments_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_date` date DEFAULT NULL,
  `payments` int(11) DEFAULT '0',
  `subscriptions` int(11) DEFAULT '0',
  `total_amount` int(11) DEFAULT '0',
  `mode` tinyint(2) DEFAULT '0' COMMENT '0: Simulation,1: Real',
  `detail` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `wp_users_transactions` ADD COLUMN `log_id` int(11) DEFAULT 0;
