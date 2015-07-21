<?php

namespace MicroServices;

class MicroServices
{

    /**
     * @link https://redmine.gosource.com.au/projects/compliancetest/wiki/Micro-Services#Request-9
     * @param $profileId
     */
    public static function prepareRunRequest($profileId)
    {
        global $wpdb;
        $profileFieldsToChange = array(
            'type_id'      => $wpdb->get_var("SELECT id FROM wp_community_profile_types WHERE title='Run' "),
            'type_name'    => 'Run v1.0',
            'profile_role' => 'Run'
        );
        $_POST['tags'] = array_unique($_POST['tags']);
        $status = copyProfileInstance($profileId, $profileFieldsToChange, false);
        \Tag::copyTags($profileId, $status['data']['id']);
        if (is_array($_POST['tags']) && !empty($_POST['tags'])) {
            foreach ($_POST['tags'] as $tag) {
                \Tag::assignTag($tag, $status['data']['id']);
            }
        }
        $profile = \ProfileInstance::getProfileBy('id', $profileId);
        $uniq_key = md5($profile->token . mktime());
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
                'errorsTo' => array(
                    'bucket' => get_option('aws_s3_url'),
                    'key' => "profiles/validation/{$profile->token}/{$uniq_key}.json"
                ),
                'productId' => trim($_POST['product_id']),
                'tags' => (array) $_POST['tags']
            ),
            "securityContext" => array(
                "username" => $wpdb->get_var($wpdb->prepare("SELECT harness_username FROM wp_users_subscriptions WHERE user_id = %d ", get_current_user_id()))
            )
        );
        $sqs = new \SqsWrapper(get_option('schedule_sqs_queue_name'));
        $sqs->sendMessage($message);
    }

    /**
     * @link https://redmine.gosource.com.au/projects/compliancetest/wiki/Micro-Services#Request-9
     * @param $profileId
     */
    public static function executeRunRequest($profileId, $date)
    {
        $profile = \ProfileInstance::getProfileBy('id', $profileId);
        $message = array(
            'operation' => 'executeRunRequest',
            'correlationID' => '',
            'parameters' => array(
                'schedule' => array(
                    'bucket' => get_option('aws_s3_url'),
                    'key' => "profiles/user/{$profile->token}.json"
                ),
                'startAt' => $date
            )
        );
        $sqs = new \SqsWrapper(get_option('schedule_sqs_queue_name'));
        $sqs->sendMessage($message);
    }
}