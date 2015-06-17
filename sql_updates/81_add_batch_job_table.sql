CREATE TABLE IF NOT EXISTS `wp_batch_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  `access_key` varchar(255) NOT NULL,
  `function_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wp_batch_jobs_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_job_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `wp_batch_jobs` (`id`, `identifier`, `access_key`, `function_name`, `is_active`)
VALUES (NULL, 'GENERATE_TESTING_REPORTS', '69587649cb4f2706c0d5a35f8d49a04b', 'generateTestingReport', '1');

INSERT INTO `wp_batch_jobs` (
`id` ,
`identifier` ,
`access_key` ,
`function_name` ,
`is_active`
)
VALUES (
NULL , 'SEND_TESTING_REPORTS', '18a12b6e422eb0ee99d837b94dd47fbd', 'notifyUsers', '1'
);
INSERT INTO `wp_batch_jobs_params` (
`id` ,
`batch_job_id` ,
`name` ,
`value`
)
VALUES (
NULL , '3', 'emails', 'ivansolowjew@gmail.com'
), (
NULL , '3', 'community', 'SuperStream'
);