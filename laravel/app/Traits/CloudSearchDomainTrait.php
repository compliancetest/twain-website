<?php

namespace App;

use Aws\CloudSearch\CloudSearchClient;
use Aws\CloudSearchDomain\CloudSearchDomainClient;

trait CloudSearchDomainTrait
{

    /**
     * Create CloudSearchDomainClient instance for interaction with Registry search CloudSearch domain
     * @return static
     */
    public function getRegistryEndpointCloudSearchClient($documentEndpoint = true)
    {

        $credentials = [
            'key' => \App\WpOptions::where(['option_name' => 'aws_s3_key'])->first()->option_value,
            'secret' => \App\WpOptions::where(['option_name' => 'aws_s3_secret'])->first()->option_value,
        ];

        return CloudSearchDomainClient::factory([
            'region' => 'us-west-2',
            'version' => '2013-01-01',
            'endpoint' => $documentEndpoint ? config('aws.registry_domain.' . getenv('ENVIRONMENT')) : config('aws.registry_domain_search.' . getenv('ENVIRONMENT')),
            'credentials' => $credentials
        ]);
    }

    /**
     * Create CloudSearchDomainClient instance for interaction with Fulltext search CloudSearch domain
     * @return static
     */
    public function getFulltextEndpointCloudSearchClient($documentEndpoint = true)
    {

        $credentials = [
            'key' => \App\WpOptions::where(['option_name' => 'aws_s3_key'])->first()->option_value,
            'secret' => \App\WpOptions::where(['option_name' => 'aws_s3_secret'])->first()->option_value,
        ];

        return CloudSearchDomainClient::factory([
            'region' => 'us-west-2',
            'version' => '2013-01-01',
            'endpoint' => $documentEndpoint ? config('aws.domain.' . getenv('ENVIRONMENT')) : config('aws.domain.' . getenv('ENVIRONMENT')),
            'credentials' => $credentials
        ]);
    }
}