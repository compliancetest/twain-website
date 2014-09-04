CREATE TABLE `wp_users_extra` (
  `userID` int(11) unsigned NOT NULL,
  `purchased_tokens` int(11) DEFAULT '0',
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
