ALTER TABLE `wp_xero_payments` ADD `organisation_id` INT NOT NULL AFTER `is_reconciled` ,
ADD `payment_method_id` INT NOT NULL AFTER `organisation_id` ,
ADD `is_paid` BOOLEAN NOT NULL AFTER `payment_method_id` ,
ADD `date_paid` DATETIME NOT NULL AFTER `is_paid`;
ALTER TABLE `wp_xero_payments` CHANGE `date` `date_added` DATE NOT NULL ;
ALTER TABLE `wp_xero_payments` CHANGE `date_added` `date_added` DATETIME NOT NULL ;
ALTER TABLE `wp_xero_payments` ADD `payment_id` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `wp_xero_payments` DROP `is_reconciled` ;