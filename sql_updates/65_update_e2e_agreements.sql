ALTER TABLE `wp_e2e_agreement` ADD `claim_id` VARCHAR( 255 ) NOT NULL AFTER `claim_date` ,
ADD `token` VARCHAR( 255 ) NOT NULL AFTER `claim_id` ,
ADD `certificate` MEDIUMBLOB NOT NULL AFTER `token` ;