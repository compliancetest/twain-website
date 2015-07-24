ALTER TABLE `wp_e2e_agreement` CHANGE `token` `requester_token` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE `wp_e2e_agreement` CHANGE `certificate` `requester_certificate` MEDIUMBLOB NOT NULL ;
ALTER TABLE `wp_e2e_agreement` ADD `responder_token` VARCHAR( 255 ) NOT NULL ,
ADD `responder_certificate` MEDIUMBLOB NOT NULL ;
