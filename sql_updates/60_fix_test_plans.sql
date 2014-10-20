ALTER TABLE `wp_test_plans` DROP COLUMN `organisation_id`;
ALTER TABLE `wp_test_plans` ADD COLUMN `organisation_subscription_id` INT(11) AFTER `id`;