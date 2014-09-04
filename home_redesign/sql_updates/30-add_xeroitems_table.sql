CREATE TABLE IF NOT EXISTS `wp_xeroitems` (
  `id` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `unit_price` int(11) NOT NULL,
  `account_code` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;