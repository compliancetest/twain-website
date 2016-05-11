<?php

$data = [
    'bucket' => [
        'transactions' => getenv('BUCKET_TRANSACTIONS'),
        'website' => getenv('BUCKET_WEBSITE'),
        'region' => getenv('BUCKET_REGION'),
    ],
    'env' => getenv('ENVIRONMENT')
];

return $data;