CREATE TABLE `wp_xero_accounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `AcountID` varchar(255) DEFAULT NULL,
  `Code` varchar(100) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL,
  `Type` varchar(100) DEFAULT NULL,
  `TaxType` varchar(250) DEFAULT NULL,
  `Description` varchar(500) DEFAULT NULL,
  `Class` varchar(100) DEFAULT NULL,
  `EnablePaymentsToAccount` varchar(10) DEFAULT NULL,
  `ShowInExpenseClaims` varchar(10) DEFAULT NULL,
  `ReportingCode` varchar(100) DEFAULT NULL,
  `HasAttachments` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
