ALTER TABLE `wp_e2e_agreement_log` CHANGE `sent_by_user_id` `sent_by_user` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `wp_e2e_agreement_log` ADD `state` INT NOT NULL AFTER `agreement_id` ;
ALTER TABLE `wp_e2e_agreement_log` CHANGE `state` `state` VARCHAR( 255 ) NOT NULL ;