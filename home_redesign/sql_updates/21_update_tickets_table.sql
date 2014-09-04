ALTER TABLE `wp_ticket_priorities` CHANGE `price` `price` int(11) DEFAULT 0;
ALTER TABLE `wp_tickets` CHANGE `price` `price` int(11) DEFAULT 0;
ALTER TABLE `wp_tickets` ADD COLUMN `total_price` int(11) DEFAULT 0 AFTER `price`;
ALTER TABLE `wp_tickets` ADD COLUMN `pending_amount` double(10, 2) DEFAULT 0;