ALTER TABLE `wp_compliance_claims` ADD COLUMN `claim_id` varchar(500) AFTER `id`;
ALTER TABLE `wp_compliance_claims` ADD COLUMN `certificate` MEDIUMBLOB;
ALTER TABLE `wp_compliance_claims` ADD COLUMN `token` VARCHAR(30);