ALTER TABLE `wp_e2e_agreement`
  DROP `requestor_audit_log`,
  DROP `responder_audit_log`,
  DROP `requester_certificate`,
  DROP `responder_certificate`;

ALTER TABLE `wp_compliance_claims`
DROP `certificate`;