ALTER TABLE wp_users_purchases ADD COLUMN `signup_fee` INT (11);
ALTER TABLE wp_users_purchases CHANGE `price` `monthly_fee` INT (11);