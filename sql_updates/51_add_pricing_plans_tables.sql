CREATE TABLE IF NOT EXISTS `wp_pricing_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_str` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ;


INSERT INTO `wp_pricing_plans` (`id`, `id_str`, `title`, `description`, `type`) VALUES
(1, 'SS-CONT-EMP-BASIC', 'Employer Basic', 'Test the alternate file format (AFF), with testing of new versions intermittently', 'Suite'),
(2, 'SS-CONT-EMP-STD', 'Employer Standard', 'Low volume, functional testing of the full range of test cases', 'Suite'),
(3, 'SS-CONT-EMP-ADV', 'Employer Advanced', 'Low volume, functional testing of the full range of test cases, including support for large messages ', 'Suite'),
(4, 'SS-CONT-FUND-STD', 'Fund/SMSF Standard', 'Functional testing for funds and SMSF Solution Providers', 'Suite'),
(5, 'SS-CONT-FUND-ADV', 'Fund/SMSF Advanced', 'Functional testing for funds and SMSF Solution Providers, including support for large messages', 'Suite'),
(6, 'SS-CONT-FUND-ADV-P', 'Fund/SMSF Advanced Prepaid', 'Functional testing for funds and SMSF Solution Providers, including support for large messages', 'Suite'),
(7, 'SS-CONT-ALL', 'All roles/levels Advanced', 'All features of the test suite', 'Suite');

CREATE TABLE IF NOT EXISTS `wp_pricing_plans_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pricing_plan_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `wp_pricing_plans_attributes` (`id`, `pricing_plan_id`, `name`, `type`, `value`, `title`, `description`) VALUES
(1, 1, 'Signup', 'itemcode', 'SS-CONT-EMP-BASIC-SU', 'Signup Fee', 'Once off fee charged when a subscription is initially purchased'),
(2, 1, 'Monthly', 'itemcode', 'SS-CONT-EMP-BASIC-MS', 'Monthly Fee', 'Charge per month to maintain the subscription'),
(3, 1, 'BillingType', 'string', 'TimeOfUse', 'Billing Arrangement', 'Monthly charge is incurred whenever the account is used to send messages in a given calendar month'),
(4, 1, 'Employer', 'role', 'AFF', '', ''),
(5, 2, 'Signup', 'itemcode', 'SS-CONT-EMP-STD-SU', 'Signup Fee', 'Once off fee charged when a subscription is initially purchased'),
(6, 2, 'Monthly', 'itemcode', 'SS-CONT-EMP-STD-MS', 'Monthly Fee', 'Charge per month to maintain the subscription'),
(7, 2, 'BillingType', 'string', 'Monthly', 'Billing Arrangement', 'Monthly charge incurred in advance for each month the subscription is active'),
(8, 2, 'Employer', 'role', 'AFF, B, A', '', ''),
(9, 3, 'Signup', 'itemcode', 'SS-CONT-EMP-ADV-SU', 'Signup Fee', 'Once off fee charged when a subscription is initially purchased'),
(10, 3, 'Monthly', 'itemcode', 'SS-CONT-EMP-ADV-MS', 'Monthly Fee', 'Charge per month to maintain the subscription'),
(11, 3, 'BillingType', 'string', 'Monthly', 'Billing Arrangement', 'Monthly charge incurred in advance for each month the subscription is active'),
(12, 3, 'Employer', 'role', 'AFF, B, A, BULK', '', ''),
(14, 4, 'Signup', 'itemcode', 'SS-CONT-SU', 'Signup Fee', 'Once off fee charged when a subscription is initially purchased'),
(15, 4, 'Monthly', 'itemcode', 'SS-CONT-MS', 'Monthly Fee', 'Charge per month to maintain the subscription'),
(16, 4, 'BillingType', 'string', 'Monthly', 'Billing Arrangement', 'Monthly charge incurred in advance for each month the subscription is active'),
(17, 4, 'Fund', 'role', 'AFF, B, A, BULK', '', ''),
(18, 4, 'SMSF', 'role', 'AFF, B, A, BULK', '', ''),
(19, 5, 'Signup', 'itemcode', 'SS-CONT-FUND-ADV-SU', 'Signup Fee', 'Once off fee charged when a subscription is initially purchased'),
(20, 5, 'Monthly', 'itemcode', 'SS-CONT-FUND-ADV-MS', 'Monthly Fee', 'Charge per month to maintain the subscription'),
(21, 5, 'BillingType', 'string', 'Monthly', 'Billing Arrangement', 'Monthly charge incurred in advance for each month the subscription is active'),
(22, 5, 'Fund', 'role', 'AFF, B, A, BULK', '', ''),
(23, 5, 'SMSF', 'role', 'AFF, B, A, BULK', '', ''),
(24, 6, 'Signup', 'itemcode', 'itemcode', 'Signup Fee', 'Once off fee charged when a subscription is initially purchased'),
(25, 6, 'Monthly', 'itemcode', 'SS-CONT-FUND-ADV-P-MS', 'Monthly Fee', 'Charge per month to maintain the subscription'),
(26, 6, 'BillingType', 'string', 'Prepaid', 'Billing Arrangement', 'All fees for the prepayment period are paid upfront'),
(27, 6, 'Period', 'number', '12', 'Prepayment Period', 'Length in months of the prepaid period'),
(28, 6, 'Fund', 'role', 'AFF, B, A, BULK', '', ''),
(29, 6, 'SMSF', 'role', 'AFF, B, A, BULK', '', ''),
(30, 7, 'Signup', 'itemcode', 'SS-CONT-ALL-ADV-SU', 'Signup Fee', 'Once off fee charged when a subscription is initially purchased'),
(31, 7, 'Monthly', 'itemcode', 'SS-CONT-ALL-ADV-MS', 'Monthly Fee', 'Charge per month to maintain the subscription'),
(32, 7, 'BillingType', 'string', 'Monthly', 'Billing Arrangement', 'Monthly charge incurred in advance for each month the subscription is active'),
(33, 7, 'Fund', 'role', 'AFF, B, A, BULK', '', ''),
(34, 7, 'SMSF', 'role', 'AFF, B, A, BULK', '', ''),
(35, 7, 'Clearing House', 'role', 'AFF, B, A, BULK', '', ''),
(36, 7, 'Employer', 'role', 'AFF, B, A, BULK', '', '');