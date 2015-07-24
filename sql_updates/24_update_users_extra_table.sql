ALTER TABLE wp_users_extra ADD COLUMN `subscriptions` INT(11) DEFAULT 0;
ALTER TABLE wp_users_extra ADD COLUMN `cards` INT(11) DEFAULT 0;
ALTER TABLE wp_users_extra ADD COLUMN `total_ticket_hours_urgent` INT(11) DEFAULT 0;
ALTER TABLE wp_users_extra ADD COLUMN `total_ticket_hours_high` INT(11) DEFAULT 0;
ALTER TABLE wp_users_extra ADD COLUMN `total_ticket_hours_normal` INT(11) DEFAULT 0;
ALTER TABLE wp_users_extra ADD COLUMN `pending_ticket_hours_urgent` INT(11) DEFAULT 0;
ALTER TABLE wp_users_extra ADD COLUMN `pending_ticket_hours_high` INT(11) DEFAULT 0;
ALTER TABLE wp_users_extra ADD COLUMN `pending_ticket_hours_normal` INT(11) DEFAULT 0;