CREATE TABLE IF NOT EXISTS `wp_compliance_claims_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `claim_id` int(11) NOT NULL,
  `conv_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;