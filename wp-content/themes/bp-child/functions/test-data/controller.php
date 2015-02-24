<?php
/**
* Test Data  
*/

function saveProfileType()
{
    global $wpdb;
    $content = stripslashes($_POST['profile_type_text']);
    $file = $_FILES['profile_type_file'];
    
    $community_id = $_POST['community_id'];
    $type_id = isset($_POST['type_id']) ? $_POST['type_id'] : null;
    $user_id = get_current_user_id();
    
    if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }
    
    if($file['error'] == UPLOAD_ERR_OK && $file['size'] > 0)
    {
        $fp = fopen($file['tmp_name'], 'r');
        $content = fread($fp, filesize($file['tmp_name']));
        fclose($fp);
        $_POST['profile_type_text'] = $content;
    }
    
    $schemaObj = json_decode($content);
    if(!$schemaObj || !isset($schemaObj->title) || !$schemaObj->title)
    {
        addMessage('The profile format does not follow the format expected.', 'error');
        return false;
    }
    
    if($type_id)
    {
        //Validate Type ID
        $query = $wpdb->prepare("SELECT id FROM ". $wpdb->prefix . "community_profile_types WHERE id=%d AND community_id=%d", $type_id, $community_id);
        $id = $wpdb->get_var($query);
        if(!$id)
        {
            addMessage('Invaild Request!', 'error');
            return;
        }
        $result = $wpdb->update($wpdb->prefix . "community_profile_types", 
                                array('community_id' => $community_id, 'title' => $schemaObj->title, 'creator_id' => $user_id, 'created_date' => date('Y-m-d H:i:s'), 'schema' => base64_encode($content)),
                                array('id' => $type_id));        
    }else{
        $wpdb->insert($wpdb->prefix . "community_profile_types", 
                                array('community_id' => $community_id, 'title' => $schemaObj->title, 'creator_id' => $user_id, 'created_date' => date('Y-m-d H:i:s'), 'schema' => base64_encode($content)));
    }
    BlobsMigration::uploadProfileTypes();
    addMessage('Profile Type successfully saved!');
    $group = groups_get_group(array('group_id' => $community_id));
    
    wp_redirect(bp_get_group_admin_permalink($group));
    exit;
    
}

function deleteProfileType()
{
    global $wpdb;
    
    $type_id = $_REQUEST['type_id'];
    $community_id = $_REQUEST['community_id'];
    $user_id = get_current_user_id();
    
    if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d AND community_id=%d", $type_id, $community_id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }
    
    if($row->instances > 0)
    {
        addMessage("Sorry, you can't delete the profile type because it still includes some instances.", "error");
        return;
    }
    
    $wpdb->delete($wpdb->prefix . "community_profile_types", array('id' => $row->id));
    addMessage("The profile type was deleted.");
    $group = groups_get_group(array('group_id' => $community_id));    
    wp_redirect(bp_get_group_admin_permalink($group));
    
    exit;
}

function readProfileType()
{
    global $wpdb;
    
    $type_id = $_REQUEST['type_id'];
    $community_id = $_REQUEST['community_id'];
    $user_id = get_current_user_id();
    
    header('Content-type: application/xml');
    
    if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
    {
        echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
        exit;
    }
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d AND community_id=%d", $type_id, $community_id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
        exit;
    }
    
    echo '<result>';
    echo '<status>success</status>';
    echo '<schema><![CDATA[' . base64_decode($row->schema) . ']]></schema>';
    echo '<id>' . $row->id . '</id>';
    echo '</result>';
    exit;
}

function createUIFromProfileType($action)
{
    global $wpdb;
    
    $type_id = $_REQUEST['profile-type-id'];
    $instance_id = $_REQUEST['instance-id'];
    
    $user_id = get_current_user_id();
    
    header('Content-type: application/xml');
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $type_id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        echo '<result><status>error</status><message>Invalid Request!</message></result>';
        exit;
    }
    
    $community_id = $row->community_id;
    
    if(wp_verify_nonce($action, 'get-harness-profile-ui'))
    {
        $instance_type = 'harness';
        
        if(!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin())
        {
            echo '<result><status>error</status><message>Invalid Request!</message></result>';
            exit;    
        }
    }else if(wp_verify_nonce($action, 'get-tester-profile-ui')){
        $instance_type = 'tester';
        $community_ids = getUserSubscribedCommunities($user_id);
        if(!in_array($community_id, $community_ids))
        {
            echo '<result><status>error</status><message>Invalid Request!</message></result>';
            exit;    
        }
    }
    
    if(!$instance_id)
    {
        echo '<result><status>success</status><schema><![CDATA[' . base64_decode($row->schema) . ']]></schema><data>{}</data></result>';    
    }else{
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d AND community_id=%d", $instance_id, $community_id);
        $instance_row = $wpdb->get_row($query);
        if(!$instance_row)
        {
            echo '<result><status>error</status><message>Invalid Request!</message></result>';
            exit;    
        }else{
            $json = S3Wrapper::getProfile( $instance_row->token, true );
            if( strlen( $json ) > get_option( 's3_xml_max_size' ) ){
                echo '<result><status>error</status><schema><![CDATA[' . base64_decode($row->schema) . ']]></schema><message>Profile is too large to edit online. Please download and edit locally, then upload.</message><type>s3_xml_max_size</type></result>';
            } else {
                echo '<result><status>success</status><schema><![CDATA[' . base64_decode($row->schema) . ']]></schema><data>' . updateSpecialChars( $json ) . '</data></result>';
            }
        }
    }
    
    exit;
}

function updateSpecialChars($content) 
{
    $content = str_replace('&amp;', '&', $content);
    $content = str_replace('&', '&amp;', $content);
    return $content;
}

function saveProfileInstance($action)
{
    global $wpdb;

    $type_id = $_REQUEST['profile-type-id'];
    $instance_id = $_REQUEST['instance-id'];
    $user_id = get_current_user_id();
    $instance_type = '';

    header('Content-type: application/xml');

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $type_id);
    $profile_type = $wpdb->get_row($query);

    if (!$profile_type) {
        echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
        exit;
    }

    $community_id = $profile_type->community_id;

    if (wp_verify_nonce($action, 'save-harness-instance')) {
        $instance_type = 'harness';

        if (!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin()) {
            echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
            exit;
        }
    } else if (wp_verify_nonce($action, 'save-tester-instance')) {
        $instance_type = 'tester';
        $community_ids = getUserSubscribedCommunities($user_id);
        if (!in_array($community_id, $community_ids)) {
            echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
            exit;
        }
    }

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d AND community_id=%d", $type_id, $community_id);
    $profile_type = $wpdb->get_row($query);

    if ($instance_id) {
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d AND community_id=%d", $instance_id, $community_id);
        $profile_instance = $wpdb->get_row($query);

        if (!$profile_instance) {
            echo '<result><status>error</status><msg>Invalid Request!</msg></result>';
            exit;
        }
    }


    //Getting Data
    $data = stripcslashes($_POST['data']);
    $jsonData = base64_encode($data);
    $max_file_size_conf = get_option('uploads_files_max_size');
    if (strlen($data) > $max_file_size_conf * 1024 * 1024) {
        echo '<result><status>error</status><msg>The file you have attempted to upload exceeds the system limit of ' . $max_file_size_conf . 'MB</msg></result>';
        exit;
    }
    $jsonObject = json_decode($data);
    $token = $instance_id ? $wpdb->get_var($wpdb->prepare("SELECT token FROM wp_community_profile_instances WHERE id = %d ", $instance_id)) : sha1(time() . $jsonObject->Profile->Title . rand(0, 9999) . $type_id . $community_id);
    $s3 = new S3Wrapper();
    $s3->putObject('/profiles/user/' . $token . '.json', $data);
    $file_size = strlen($data);
    //if backend validation enabled
    $status = 'valid';
    $validation_url = '';

    $profileType = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_community_profile_types WHERE id = %d ", $type_id));
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
                'username' => $wpdb->get_var($wpdb->prepare("SELECT harness_username FROM wp_users_subscriptions WHERE user_id = %d ", $user_id))
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
    } else {
        $profile_name = $jsonObject->Profile->Title . ' v' . $jsonObject->Profile->Version->Major . '.' . $jsonObject->Profile->Version->Minor;
        if (!empty($jsonObject->Profile->Version->Patch)) {
            $profile_name .= '.' . $jsonObject->Profile->Version->Patch;
        }
        $profile_description = $jsonObject->Profile->Description;
        $profile_purpose = $jsonObject->Profile->Purpose;
    }
    if ($instance_id) {
        $data = array(
            'type' => $instance_type,
            'type_id' => $type_id,
            'type_name' => $type_name,
            'community_id' => $community_id,
            'filename' => '',
            'content' => $jsonData,
            'created_date' => date('Y-m-d H:i:s'),
            'creator_id' => $user_id,
            'validation_status' => $status,
            'validation_url' => $validation_url,
            'content_length' => $file_size
        );
        if (get_option('validate_via_sqs') != 'yes') {
            $data['profile_name'] = $profile_name;
            $data['profile_description'] = $profile_description;
            $data['purpose'] = $profile_purpose;
        }
        $wpdb->update($wpdb->prefix . "community_profile_instances",
            $data,
            array('id' => $instance_id)
        );
    } else {
        $wpdb->insert($wpdb->prefix . "community_profile_instances",
            array(
                'type' => $instance_type,
                'profile_name' => $profile_name,
                'profile_description' => $profile_description,
                'purpose' => $profile_purpose,
                'type_id' => $type_id,
                'type_name' => $type_name,
                'community_id' => $community_id,
                'filename' => '',
                'content' => $jsonData,
                'created_date' => date('Y-m-d H:i:s'),
                'creator_id' => $user_id,
                'token' => $token,
                'validation_status' => $status,
                'validation_url' => $validation_url,
                'content_length' => $file_size
            )
        );
        $instance_id = $wpdb->insert_id;
        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "community_profile_types SET `instances`=`instances` + 1 WHERE id=%d", $type_id));

    }
    if (get_option('validate_via_sqs') != 'yes') {
        $wpdb->delete($wpdb->prefix . 'community_profile_meta', array('profile_id' => $instance_id), '%d');

        $profile_meta = getProfileMetaData($jsonObject);
        foreach ($profile_meta as $meta_key => $meta_value) {
            if (is_array($instance_id) || is_array($meta_key) || is_array($meta_value)) {
                continue;
            }
            $wpdb->insert($wpdb->prefix . "community_profile_meta", array(
                'profile_id' => $instance_id,
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
            ));
        }
    }

    echo '<result><status>success</status></result>';
    exit;
}

function getProfileMetaData($data, $meta_key = '', $level = 0) {
    $ret = array();

    if( $data ){
        foreach ($data as $key => $value) {
            if ($level == 0 && !in_array($key, array('Profile', 'Entity', 'Fund')))
                continue;
            if (!is_object($value)) {
                $ret[($meta_key == '') ? ($key) : ($meta_key.'_'.$key)] = $value;
                continue;
            }
            $ret = array_merge($ret, getProfileMetaData($value, ($meta_key == '') ? ($key) : ($meta_key.'_'.$key), $level + 1));
        }
    }
    return $ret;
}

function deleteProfileTypeInstance($action)
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    $ids = array();
    
    $user_id = get_current_user_id();
    
    if (!is_array($id)) {
        $ids[] = $id;
    } else {
        $ids = $id;
    }
    
    foreach ($ids as $id) {
    
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $id);
        $row = $wpdb->get_row($query);
        
        if(!$row)
        {
            addMessage('Invalid Request!', 'error');
            return;
        }
        
        $redirect = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : cp_get_group_permalink_by_id($row->community_id) . "testdata";
        
        if( (wp_verify_nonce($action, 'delete-harness-instance') && !groups_is_user_admin($user_id, $row->community_id)) || (wp_verify_nonce($action, 'delete-profile-instance') && $row->creator_id != $user_id ) )
        {
            addMessage('Permission Denied!', 'error');        
            wp_redirect($redirect);
            exit;
        }
        
        $wpdb->delete($wpdb->prefix . "community_profile_instances", array('id' => $row->id));
        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "community_profile_types SET `instances`=`instances` - 1 WHERE id=%d AND `instances` > 0", $row->type_id));
        $wpdb->delete($wpdb->prefix . "community_profile_meta", array('profile_id' => $row->id));
        
        add_filter( 'query', 'wp_db_null_value' );
        $wpdb->update($wpdb->prefix . "users_subscriptions", 
            array('profile_id' => 'NULL', 'gateway_id' => 'NULL', 'entity_id' => '', 'entity_type' => '', 'alias' => ''),
            array('profile_id' => $id)
        );
        remove_filter( 'query', 'wp_db_null_value' );
    }
    
    addMessage('Profile instance was removed.');
    wp_redirect($redirect);
    exit;
}

function copyProfileInstance($action)
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    
    $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $id);
    $row = $wpdb->get_row($query, ARRAY_A);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    $redirect = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : cp_get_group_permalink_by_id($row['community_id']) . "testdata";
  
    // Copy harness profile instance (James)
    $content = S3Wrapper::getProfile( $row['token'] );
    $row['token_original'] = $row['token'];
    $row['token'] = sha1(time() . rand(0, 9999) . $row['type_id'] . $row['community_id']);
    $row['type'] = 'tester';
    $row['creator_id'] = $user_id;
    $row['created_date'] = date('Y-m-d F:i:s');
    unset($row['id']);

    $row['content'] = base64_encode( stripslashes( json_encode($content ) ) );

    //save new profile to S3
    $s3 = new S3Wrapper();
    $d = json_encode( $content );
    $s3->putObject( '/profiles/user/'.$row['token'].'.json',  $d );
    $file_size = strlen( $d );
    if( get_option('validate_via_sqs') == 'yes' ){
        $row['profile_name'] = 'Pending...';
        $row['profile_description'] = 'Pending...';
        $row['purpose'] = 'Pending...';
        $row['validation_status'] = 'pending';

        $error_format = get_option( 'validation_error_format' );
        if( empty( $error_format ) ){
            $error_format = 'html';
        }

        $profileType = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_community_profile_types WHERE id = %d ", $row['type_id'] ) );
        $profile_json = base64_decode( $profileType->schema );
        $profile_array = json_decode( $profile_json, 1 );
        $profile_type = str_replace( ' ', '', $profile_array['title'] );
        $file_name = $profile_type.'_v'.$profile_array['Version']['Major'].'_'.$profile_array['Version']['Minor'];
        $type_name = $profile_array['title'].' v'.$profile_array['Version']['Major'].'.'.$profile_array['Version']['Minor'];
        if( isset( $profile_array['Version']['Patch'] ) ){
            $file_name = $file_name.'_'.$profile_array['Version']['Patch'];
            $type_name .= '.'.$profile_array['Version']['Patch'];
        }
        $uniq_key = md5( $row['token'] . mktime() );
        $message = array(
            'operation'     => 'profileValidationRequest',
            'correlationID' => 'd4342fsc5-fa89-44f6-9286-c38a751dbac',
            'securityContext' => array(
                'username' => $wpdb->get_var( $wpdb->prepare( "SELECT harness_username FROM wp_users_subscriptions WHERE user_id = %d ", $user_id ) )
            ),
            'parameters' => array(
                'outputFormat' => $error_format,
                'document' => array(
                    'bucket' => get_option( 'aws_s3_url' ),
                    'key'    => "profiles/user/{$row['token']}.json"
                ),
                'schema' => array(
                    'bucket' => get_option( 's3_reference_bucket' ),
                    'key'    => 'schema/profiles/'.strtolower( $type_name ).'/'.$file_name.'.json'
                ),
                'saveTo' => array(
                    'bucket' => get_option( 'aws_s3_url' ),
                    'key'    => "profiles/validation/{$row['token']}/{$uniq_key}.".$error_format
                )
            )
        );
        $sqs = new SqsWrapper();
        $is_bulk = false;
        if( $file_size >= get_option( 's3_bulk_treshold' ) ){
            $is_bulk = true;
        }
        $sqs->sendMessage( $message, $is_bulk );
    }
    $wpdb->insert($wpdb->prefix . "community_profile_instances", $row);
    $new_profile_id = $wpdb->insert_id;

    if( get_option('validate_via_sqs') != 'yes' ) {
        $profile_meta = getProfileMetaData($content);
        foreach ($profile_meta as $meta_key => $meta_value) {
            $wpdb->insert($wpdb->prefix . "community_profile_meta", array(
                'profile_id' => $new_profile_id,
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
            ));
        }
    }

    $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "community_profile_types SET `instances`=`instances` + 1 WHERE id=%d", $row['type_id']));
    
    //------------------------------------------------------------------------------------------------------------------
    
    addMessage('Profile instance was copied.');
    wp_redirect($redirect);
    exit;
}

function downloadProfileTypeInstance()
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    
    $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    $filename = $row->profile_name;
    
    $content_json = S3Wrapper::getProfile( $row->token );
    if($content_json->Profile->Version)
    {
        $version = array();
        foreach(get_object_vars($content_json->Profile->Version) as $k=>$v)      
        {
            $version[] = $v;
        }
        $filename .= '_v' . implode(".", $version);
    }
    
    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=" . sanitize_file_name($filename . ".json"));
    
    echo json_encode( $content_json );
    
    exit;
}

function downloadProfileType()
{
    global $wpdb;
    
    $type_id = $_REQUEST['type_id'];
    
    $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $type_id);

    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    $filename = sanitize_file_name($row->title);
    
    $schema = base64_decode($row->schema);
    $schema_json = json_decode($schema);
    if($schema_json->Version)
    {
        $version = array();
        foreach(get_object_vars($schema_json->Version) as $k=>$v)      
        {
            $version[] = $v;
        }
        $filename .= '_v' . implode(".", $version);
    }
    
    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=" . $filename . ".json");
    
    echo $schema;
    exit;
}



function viewProfileType()
{
    global $wpdb;
    
    $type_id = $_REQUEST['id'];
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $type_id);
    $row = $wpdb->get_row($query);
        
    if(!$type_id)
    {
        ?>
        <div class="popup-box view-profile-type-box" style="display: none; width: 900px;" id="view-profile-type-box<?php echo $boxId?>">
            <div class="popup-box-header radius6 noradiusbottom">Error</div>        
            <div class="popup-box-content grid-box-body">                    
                <p class="message error">Invalid Request!</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">                
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                <div class="clear"></div>
            </div>                        
            <a class="close_btn"></a>                        
        </div>
        <?php
    }else{
        $boxId = time();
        ?>
        <div class="popup-box view-profile-type-box" style="display: none; width: 900px;" id="view-profile-type-box<?php echo $boxId?>">
            <div class="popup-box-header radius6 noradiusbottom">Profile Type Detail</div>        
            <div class="popup-box-content grid-box-body">                    
                <div id="json-view-panel<?php echo $boxId?>" class="json-view-panel"><?php echo base64_decode($row->schema)?></div>                
            </div>
            
            <div class="popup-box-footer radius6 noradiustop">                                                
                <a href="<?php echo cp_get_group_permalink_by_id($row->community_id)?>testdata?td-action=<?php echo wp_create_nonce('download-profile-type')?>&type_id=<?php echo $row->id?>" target="blank" class="action-btn process-btn"><span class="p"></span><span class="t">Download</span></a>
                <?php if(isset($_REQUEST['back'])){ ?>
                    <a href="#trigger-message-box" class="action-btn cancel-btn" rel="custom-popup" cp-type="inline"><span class="p"></span><span class="t">Close</span></a>
                <?php }else{?>
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
                <?php } ?>
                <div class="clear"></div>
            </div>                        
            <?php if(!isset($_REQUEST['back'])){ ?>
            <a class="close_btn"></a>                    
            <?php }?>                               
        </div>
        <script type="text/javascript">
            var t_data = Jsonary.create(<?php echo base64_decode($row->schema)?>).readOnlyCopy();
            var t_element = document.getElementById('json-view-panel<?php echo $boxId?>');
            Jsonary.render(t_element, t_data);                
        </script>
        <?php
    }
    exit;
}

function viewProfileInstance()
{
    global $wpdb;
    
    $instance_id = $_REQUEST['id'];
    
    $query = $wpdb->prepare("SELECT i.*, t.title as profile_type_title FROM " . $wpdb->prefix . "community_profile_instances AS i LEFT JOIN " . $wpdb->prefix ."community_profile_types AS t ON t.id=i.type_id WHERE i.id=%d", $instance_id);
    $row = $wpdb->get_row($query);
    $row->content = S3Wrapper::getProfile( $row->token, true );

    if(!$row)
    {
        ?>
        <div class="popup-box" style="display: none; width: 900px;">
            <div class="popup-box-header radius6 noradiusbottom">Error</div>        
            <div class="popup-box-content grid-box-body">                    
                <p class="message error">Invalid Request!</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">                
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                <div class="clear"></div>
            </div>                        
            <a class="close_btn"></a>                        
        </div>
        <?php
    }else{
        $boxId = time();
        ?>
        <div class="popup-box" style="display: none; width: 900px;" id="view-profile-instance-box<?php echo $boxId?>">
            <div class="popup-box-header radius6 noradiusbottom">Profile Instance Detail</div>        
            <div class="popup-box-content grid-box-body">                            
                <a href="#" class="action-btn process-btn left zcliplink" data-id="profile-url<?php echo $row->id?>"><span class="p"></span><span class="t">Copy URL</span></a>
                <input type="text" readonly="readonly" value="<?php echo get_site_url()?>/get-profile?id=<?php echo $row->token?>" class="input width60P left" id="profile-url<?php echo $row->id?>" />
                <div class="clear"></div>
                <div id="json-view-panel<?php echo $boxId?>" class="json-view-panel">
                    <?php if( strlen( $row->content ) > get_option( 's3_xml_max_size' ) ):?>
                        <div class="message error">
                            Profile is too large to edit online. Please download and edit locally, then upload.
                        </div>
                    <?php else:?>
                        <?php echo $row->content?>
                    <?php endif;?>
                </div>
            </div>
            
            <div class="popup-box-footer radius6 noradiustop">
                <?php if( strlen( $row->content ) < get_option( 's3_xml_max_size' ) ):?>
                    <a href="<?php echo S3Wrapper::getProfileLink( $row->token, true );?>" target="blank" class="action-btn process-btn"><span class="p"></span><span class="t">Download</span></a>
                <?php endif;?>
                <?php if(isset($_REQUEST['back'])){ ?>
                <a href="#trigger-message-box" class="action-btn cancel-btn" rel="custom-popup" cp-type="inline"><span class="p"></span><span class="t">Close</span></a>            
                <?php }else{ ?>
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
                <?php } ?>
                <div class="clear"></div>
                <div class="message success zclipsucces-msg displaynone">The profile url has been copied to clipboard.</div>
            </div>                        
            <?php if(!isset($_REQUEST['back'])){ ?>
            <a class="close_btn"></a>                    
            <?php }?>                       
        </div>
        <?php if( strlen( $row->content ) < get_option( 's3_xml_max_size' ) ):?>
            <script type="text/javascript">
                var t_data = Jsonary.create(<?php echo $row->content?>).readOnlyCopy();
                var t_element = document.getElementById('json-view-panel<?php echo $boxId?>');
                Jsonary.render(t_element, t_data);

            </script>
        <?php endif;?>
        <?php
    }
    exit;
}

function downloadProfileError()
{
    $filename = $_REQUEST['profile-name'] . '_validation_errors';
    $errors = $_REQUEST['profile-errors'];
    
    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=" . $filename . ".log");
    
    echo $errors;
    exit;
}

function updateProfileLookup() {
    global $wpdb;
    
    $id = isset($_REQUEST['id']) ? ($_REQUEST['id']) : (0);
    $status = isset($_REQUEST['status']) ? ($_REQUEST['status']) : (0);
    if ($id) {
        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "community_profile_instances SET `lookup`= %d WHERE id=%d", $status, $id));
    }
    echo 'success';
    exit;
}