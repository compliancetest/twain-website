CREATE TABLE IF NOT EXISTS `wp_test_plans_excluded_cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_plan_id` int(11) NOT NULL,
  `test_case_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `excluded_by_user_id` int(11) NOT NULL,
  `date` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB