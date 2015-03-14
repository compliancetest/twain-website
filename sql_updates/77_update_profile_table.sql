ALTER TABLE `wp_community_profile_instances` CHANGE `content` `content` LONGBLOB NULL DEFAULT NULL ;
ALTER TABLE `wp_community_profile_instances` ADD `profile_description` TEXT NOT NULL AFTER `profile_name` ;
ALTER TABLE `wp_community_profile_instances` ADD `type_name` VARCHAR( 100 ) NOT NULL AFTER `type_id` ;