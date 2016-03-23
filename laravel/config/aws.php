<?php

use Aws\Laravel\AwsServiceProvider;

$data = [
    'region' => 'us-west-2',
    'version' => 'latest',
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ]
];
if(getenv('APP_ENV') == 'local'){
    $data['credentials'] = [
        'key' => 'AKIAI5A2F2WZFQUDE77A',
        'secret' => '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',
    ];
}

return $data;

