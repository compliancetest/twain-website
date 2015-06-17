ALTER TABLE `wp_organisations` ADD `secondary_contact_first_name` VARCHAR( 255 ) NOT NULL AFTER `contact_email` ,
ADD `secondary_contact_last_name` VARCHAR( 255 ) NOT NULL AFTER `secondary_contact_first_name` ,
ADD `secondary_contact_email` VARCHAR( 255 ) NOT NULL AFTER `secondary_contact_last_name` ;