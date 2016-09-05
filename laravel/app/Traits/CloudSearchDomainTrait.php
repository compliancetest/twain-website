<?php

namespace App;

use Aws\CloudSearch\CloudSearchClient;
use Aws\CloudSearchDomain\CloudSearchDomainClient;

trait CloudSearchDomainTrait
{

    /**
     * Create CloudSearchDomainClient instance for interaction with CloudSearch domain
     * @param $domainName
     * @param string $endpointType - DocService | Search
     * @return static
     */
    public function getDocumentEndpointCloudSearchClient($domainName, $endpointType = 'DocService')
    {

        $domain = CloudSearchClient::factory([
            'region' => 'us-west-2',
            'version' => '2013-01-01',
            'credentials' => array(
                'key' => \App\WpOptions::where(['option_name' => 'aws_s3_key'])->first()->option_value,
                'secret' => \App\WpOptions::where(['option_name' => 'aws_s3_secret'])->first()->option_value,
            )
        ]);
        $endpoint = $domain->describeDomains(['DomainNames' => [$domainName]])->getPath('DomainStatusList');
        return CloudSearchDomainClient::factory([
            'region' => 'us-west-2',
            'version' => '2013-01-01',
            'endpoint' => 'http://' . $endpoint[0][$endpointType]['Endpoint'],
            'credentials' => array(
                'key' => \App\WpOptions::where(['option_name' => 'aws_s3_key'])->first()->option_value,
                'secret' => \App\WpOptions::where(['option_name' => 'aws_s3_secret'])->first()->option_value,
            )
        ]);
    }
}