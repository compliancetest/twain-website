CREATE TABLE `wp_users_changes` (
  `change_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER UNSIGNED NOT NULL,
  `email_changed` VARCHAR(100) NOT NULL,
  `verification_code` VARCHAR(100) NOT NULL,
  PRIMARY KEY(`change_id`)
)
ENGINE = MyISAM;
