<?php

use Aws\Laravel\AwsServiceProvider;

$data = [
    'region' => 'us-west-2',
    'version' => 'latest',
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ],
    'domain' => [
        'local' => 'https://search-twain-fulltext-integration-tiep3cmrp26mcuygunmgyq43su.us-west-2.cloudsearch.amazonaws.com',
        'integration' => 'https://search-twain-fulltext-integration-tiep3cmrp26mcuygunmgyq43su.us-west-2.cloudsearch.amazonaws.com',
        'preproduction' => 'https://search-twain-fulltext-preproduction-ienflwzarmmmsmexdetc7oqmwi.us-west-2.cloudsearch.amazonaws.com',
        'production' => 'https://search-twain-fulltext-production-ydih2jkp24asgt6uxvfzoj5aju.us-west-2.cloudsearch.amazonaws.com',
    ]
];
if(env('APP_ENV') == 'local' || getenv('ENVIRONMENT') == 'local'){
    $data['credentials'] = [
        'key' => 'AKIAI5A2F2WZFQUDE77A',
        'secret' => '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',
    ];
}

return $data;

