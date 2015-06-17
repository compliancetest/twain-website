ALTER TABLE `wp_community_profile_instances` ADD `validation_status` VARCHAR( 20 ) NOT NULL ,
ADD `validation_url` VARCHAR( 255 ) NOT NULL ;

UPDATE `wp_community_profile_instances` SET `validation_status` = 'Valid'