CREATE TABLE IF NOT EXISTS `wp_xero_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(255) NOT NULL,
  `account_code` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` double(8,2) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `is_reconciled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;