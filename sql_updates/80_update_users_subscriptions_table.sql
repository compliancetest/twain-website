ALTER TABLE `wp_users_subscriptions`
ADD COLUMN `mandatory_response_validation` TINYINT(1) NULL DEFAULT '0' AFTER `force_e2e_routing`