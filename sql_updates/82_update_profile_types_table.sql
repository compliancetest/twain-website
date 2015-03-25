ALTER TABLE  `wp_community_profile_types` ADD  `is_expandable` BOOLEAN NOT NULL DEFAULT FALSE ;
update wp_community_profile_types set is_expandable = 1 where title in( 'Employer', 'Product')