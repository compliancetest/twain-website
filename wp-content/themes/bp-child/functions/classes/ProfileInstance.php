<?php

class ProfileInstance {

    public static function save( $profileData ){
        global $wpdb;
        $data = stripcslashes( $profileData['data'] );
        $jsonData = base64_encode( $data );
        $max_file_size_conf = get_option('uploads_files_max_size');
        if( strlen( $data ) > $max_file_size_conf * 1024 * 1024) {
            return array( 'status' => 'error', 'message' => 'The file you have attempted to upload exceeds the system limit of ' . $max_file_size_conf . 'MB' );
        }
        if( isset( $profileData['instance_id'] ) && ! empty( $profileData['instance_id'] ) ){
            $token = $wpdb->get_var( $wpdb->prepare( "SELECT token FROM wp_community_profile_instances WHERE id = %d ", $profileData['instance_id'] ) );
        } else{
            if( isset( $profileData['token'] ) ){
                $token = $profileData['token'];
            } else{
                $token = sha1( time() .  rand(0, 9999) . $profileData['type_id'] . $profileData['community_id'] );
            }
        }
        $s3 = new S3Wrapper();
        $s3->putObject( '/profiles/user/' . $token . '.json', $data );
        $file_size = strlen( $data );
        //if backend validation enabled
        $status = 'valid';
        $validation_url = '';

        $profileType = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_community_profile_types WHERE id = %d ", $profileData['type_id'] ) ) ;
        $profile_json = base64_decode($profileType->schema);
        $profile_array = json_decode($profile_json, 1);
        $profile_type = str_replace(' ', '', $profile_array['title']);
        $file_name = $profile_type . '_v' . $profile_array['Version']['Major'] . '_' . $profile_array['Version']['Minor'];
        $type_name = $profile_array['title'] . ' v' . $profile_array['Version']['Major'] . '.' . $profile_array['Version']['Minor'];
        if (isset($profile_array['Version']['Patch'])) {
            $file_name = $file_name . '_' . $profile_array['Version']['Patch'];
            $type_name .= '.' . $profile_array['Version']['Patch'];
        }

        if (get_option('validate_via_sqs') == 'yes') {
            $status = 'pending';

            $error_format = get_option('validation_error_format');
            if (empty($error_format)) {
                $error_format = 'html';
            }
            $uniq_key = md5($token . mktime());
            $message = array(
                'operation' => 'profileValidationRequest',
                'correlationID' => 'd4342fsc5-fa89-44f6-9286-c38a751dbac',
                'securityContext' => array(
                    'username' => $wpdb->get_var( $wpdb->prepare("SELECT harness_username FROM wp_users_subscriptions WHERE user_id = %d ", $profileData['user_id'] ) )
                ),
                'parameters' => array(
                    'outputFormat' => $error_format,
                    'document' => array(
                        'bucket' => get_option('aws_s3_url'),
                        'key' => "profiles/user/{$token}.json"
                    ),
                    'schema' => array(
                        'bucket' => get_option('s3_reference_bucket'),
                        'key' => 'schema/profiles/' . strtolower($profile_type) . '/' . $file_name . '.json'
                    ),
                    'saveTo' => array(
                        'bucket' => get_option('aws_s3_url'),
                        'key' => "profiles/validation/{$token}/{$uniq_key}." . $error_format
                    )
                )
            );
            $sqs = new SqsWrapper();
            $is_bulk = false;
            if ($file_size >= get_option('s3_bulk_treshold')) {
                $is_bulk = true;
            }
            $sqs->sendMessage($message, $is_bulk);
        }

        if (get_option('validate_via_sqs') == 'yes') {
            $profile_name = 'Pending...';
            $profile_description = 'Pending...';
            $profile_purpose = 'Pending...';
            $profile_role = null;
        } else {
            $jsonObject = json_decode( $data );
            $profile_name = $jsonObject->Profile->Title . ' v' . $jsonObject->Profile->Version->Major . '.' . $jsonObject->Profile->Version->Minor;
            if (!empty($jsonObject->Profile->Version->Patch)) {
                $profile_name .= '.' . $jsonObject->Profile->Version->Patch;
            }
            $profile_description = $jsonObject->Profile->Description;
            $profile_purpose = $jsonObject->Profile->Purpose;
            $profile_role = $profile_array['title'];
        }
        if( isset( $profileData['instance_id'] ) ) {
            $data = array(
                'type' => $profileData['type'],
                'type_id' => $profileData['type_id'],
                'type_name' => $type_name,
                'community_id' => $profileData['community_id'],
                'content' => $jsonData,
                'created_date' => date('Y-m-d H:i:s'),
                'creator_id' => $profileData['user_id'],
                'validation_status' => $status,
                'validation_url' => $validation_url,
                'content_length' => $file_size,
                'profile_role' => $profile_role
            );
            if (get_option('validate_via_sqs') != 'yes') {
                $data['profile_name'] = $profile_name;
                $data['profile_description'] = $profile_description;
                $data['purpose'] = $profile_purpose;
            }
            $wpdb->update($wpdb->prefix . "community_profile_instances",
                $data,
                array('id' => $profileData['instance_id'])
            );
        } else {
            $wpdb->insert($wpdb->prefix . "community_profile_instances",
                array(
                    'type' => $profileData['type'],
                    'profile_name' => $profile_name,
                    'profile_description' => $profile_description,
                    'purpose' => $profile_purpose,
                    'type_id' => $profileData['type_id'],
                    'type_name' => $type_name,
                    'community_id' => $profileData['community_id'],
                    'content' => $jsonData,
                    'created_date' => date('Y-m-d H:i:s'),
                    'creator_id' => $profileData['user_id'],
                    'token' => $token,
                    'token_original' => isset( $profileData['token_original'] ) ? $profileData['token_original'] : '',
                    'validation_status' => $status,
                    'validation_url' => $validation_url,
                    'content_length' => $file_size,
                    'profile_role' => $profile_role
                )
            );
            $profileData['instance_id'] = $wpdb->insert_id;
            $wpdb->query( $wpdb->prepare( "UPDATE wp_community_profile_types SET `instances`=`instances` + 1 WHERE id = %d ", $profileData['type_id'] ) );

        }
        //backend validation service populate metadata if it enabled
        if (get_option('validate_via_sqs') != 'yes') {
            //remove old meta first
            $wpdb->delete( 'wp_community_profile_meta',
                array( 'profile_id' => $profileData['instance_id'] ), '%d');

            $profile_meta = getProfileMetaData( $jsonObject );
            foreach( $profile_meta AS $meta_key => $meta_value ){
                if( is_array( $profileData['instance_id'] ) || is_array( $meta_key ) || is_array( $meta_value ) ) {
                    continue;
                }
                $wpdb->insert( "wp_community_profile_meta", array(
                    'profile_id' => $profileData['instance_id'],
                    'meta_key'   => $meta_key,
                    'meta_value' => $meta_value,
                ));
            }
        }
        return array( 'status' => 'success', 'message' => '' );
    }
}