ALTER TABLE `wp_users_subscriptions` 
ADD COLUMN `harness_key` BLOB NULL  AFTER `harness_endpoint_url` , 
ADD COLUMN `harness_certificate` BLOB NULL  AFTER `harness_key`,
ADD COLUMN `harness_certificate_p12` BLOB NULL  AFTER `harness_key`;