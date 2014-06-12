ALTER TABLE `wp_gateways` ADD COLUMN `username` VARCHAR(30) AFTER `name`,
 ADD COLUMN `password` VARCHAR(100) AFTER `name`;