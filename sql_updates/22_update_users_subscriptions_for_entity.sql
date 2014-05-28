ALTER TABLE `wp_users_subscriptions` ADD COLUMN `entity_id` VARCHAR(45) AFTER `profile_id`,
 ADD COLUMN `entity_type` VARCHAR(50) AFTER `entity_id`;