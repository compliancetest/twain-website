#DROP TABLE `wp_users_purchases`;
#DROP TABLE `wp_users_transactions`;
#DROP TABLE `wp_users_organisation_pricing`;
#DROP TABLE `wp_users_organisation_subscriptions`;
#DROP TABLE `wp_users_payments_logs`;

RENAME TABLE `wp_users_purchases` TO `wp_users_purchases_deleted`;
RENAME TABLE `wp_users_transactions` TO `wp_users_transactions_deleted`;
RENAME TABLE `wp_users_organisation_pricing` TO `wp_users_organisation_pricing_deleted`;
RENAME TABLE `wp_users_organisation_subscriptions` TO `wp_users_organisation_subscriptions_deleted`;
RENAME TABLE `wp_users_payments_logs` TO `wp_users_payments_logs_deleted`;
