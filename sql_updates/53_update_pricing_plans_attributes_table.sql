ALTER TABLE `wp_pricing_plans_attributes` ADD `visibility` BOOLEAN NOT NULL DEFAULT FALSE ;
UPDATE `wp_pricing_plans_attributes` SET visibility =1;