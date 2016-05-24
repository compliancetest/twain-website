<?php

$data = [
    'bucket' => [
        'transactions' => getenv('BUCKET_TRANSACTIONS') ? getenv('BUCKET_TRANSACTIONS') : 'captures.integration.twain.gosource.com.au',
        'website' => getenv('BUCKET_WEBSITE') ? getenv('BUCKET_WEBSITE') : 'www.integration.twain.gosource.com.au',
        'region' => getenv('BUCKET_REGION') ? getenv('BUCKET_REGION') : 'us-west-2',
    ],
    'env' => getenv('ENVIRONMENT')
];

return $data;