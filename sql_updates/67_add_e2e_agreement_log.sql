CREATE TABLE IF NOT EXISTS `wp_e2e_agreement_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agreement_id` int(11) NOT NULL,
  `sent_by` int(11) NOT NULL COMMENT '1- requester, 2 - responder',
  `sent_by_user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;