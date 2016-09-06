<?php

namespace App;

use Aws\CloudSearch\CloudSearchClient;
use Aws\CloudSearchDomain\CloudSearchDomainClient;

trait CloudSearchDomainTrait
{

    /**
     * Create CloudSearchDomainClient instance for interaction with CloudSearch domain
     * @return static
     */
    public function getDocumentEndpointCloudSearchClient()
    {

        $credentials = [
            'key' => \App\WpOptions::where(['option_name' => 'aws_s3_key'])->first()->option_value,
            'secret' => \App\WpOptions::where(['option_name' => 'aws_s3_secret'])->first()->option_value,
        ];

        return CloudSearchDomainClient::factory([
            'region' => 'us-west-2',
            'version' => '2013-01-01',
            'endpoint' => config('aws.registry_domain.integration'),// . getenv('ENVIRONMENT')),
            'credentials' => $credentials
        ]);
    }
}