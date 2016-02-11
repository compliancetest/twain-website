<?php

class BaseAWS
{
    /**
     * We are using service roles on live servers( ENVIRONMENT variable defined by default on live servers).
     * Just do not define this env variable to use AWS access key / secret
     * @return array
     */
    public static function getAWSConfigs()
    {
        $configs = array(
            'region' => 'us-west-2',
        );
        $key = get_option('aws_s3_key');
        $secret = get_option('aws_s3_secret');
        if (!$secret || !$key) {
            $configs['key'] = $key;
            $configs['secret'] = $secret;
        }

        return $configs;
    }
}