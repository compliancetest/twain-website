<?php

namespace MicroServices;


class MicroServices {

    /**
     * @link https://redmine.gosource.com.au/projects/compliancetest/wiki/Micro-Services#Request-9
     * @param $profileId
     */
    public static function prepareRunRequest( $profileId )
    {
        $profileFieldsToChange = array(
            'type_name'    => 'Run v1.0',
            'profile_role' => 'Run'
        );
        $status = copyProfileInstance( $profileId, $profileFieldsToChange );
        $profile = \ProfileInstance::getProfileBy( 'id', $profileId );
        $uniq_key = md5( $profile->token . mktime());
        $message = array(
            'operation' => 'prepareRunRequest',
            'correlationID' => '',
            'parameters' => array(
                'schedule' => array(
                    'bucket' => get_option('aws_s3_url'),
                    'key' => "profiles/user/{$profile->token}.json"
                ),
                'saveTo' => array(
                    'bucket' => get_option('aws_s3_url'),
                    'key' => "profiles/user/{$status['data']['token']}.json"
                ),
                'errorTo' => array(
                    'bucket' => get_option('aws_s3_url'),
                    'key' => "profiles/validation/{$profile->token}/{$uniq_key}.json"
                ),
                'product_id' => trim( $_POST['product_id'] ),
                'tags' => $_POST['tags']
            )
        );
        $sqs = new \SqsWrapper();
        $sqs->sendMessage( $message );
    }

    /**
     * @link https://redmine.gosource.com.au/projects/compliancetest/wiki/Micro-Services#Request-9
     * @param $profileId
     */
    public static function executeRunRequest( $profileId )
    {
        $profile = \ProfileInstance::getProfileBy( 'id', $profileId );
        $message = array(
            'operation' => 'executeRunRequest',
            'correlationID' => '',
            'parameters' => array(
                'schedule' => array(
                    'bucket' => get_option('aws_s3_url'),
                    'key' => "profiles/user/{$profile->token}.json"
                ),
                'startAt' => $_POST['datetime']
            )
        );
        $sqs = new \SqsWrapper();
        $sqs->sendMessage( $message );
    }
}