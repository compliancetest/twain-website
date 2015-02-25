ALTER TABLE `wp_community_profile_instances`
ADD COLUMN `profile_role` VARCHAR(255) DEFAULT NULL AFTER `content_length`;